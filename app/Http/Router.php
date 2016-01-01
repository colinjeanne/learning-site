<?php namespace App\Http;

use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;

class Router
{
    private $container;
    private $routes = [];
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        
        $this->container->add(Controllers\RootController::class);
        
        $this->addRoute('GET', '/', 'RootController@getIndex');
    }
    
    public function getRoutes()
    {
        return $this->routes;
    }
    
    private function addRoute($method, $path, $handler)
    {
        if (!is_callable($handler)) {
            $data = explode('@', $handler);
            $className = 'App\Http\Controllers\\' . $data[0];
            $methodName = $data[1];
            
            $handler = function (
                ServerRequestInterface $request,
                ResponseInterface $response,
                array $args
            ) use (
                $className,
                $methodName
            ) {
                $controller = $this->container->get($className);
                
                $middleware = [];
                if (method_exists($controller, 'getMiddleware')) {
                    $middleware = $controller->getMiddleware($methodName);
                }
                
                $middleware[] = function (
                    ServerRequestInterface $request,
                    ResponseInterface $response,
                    callable $next
                ) use (
                    $controller,
                    $methodName,
                    $args
                ) {
                    return call_user_func(
                        [$controller, $methodName],
                        $request,
                        $response,
                        $args
                    );
                };
                
                $relay = new RelayBuilder();
                $dispatcher = $relay->newInstance($middleware);
                return $dispatcher($request, $response);
            };
        }
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
}

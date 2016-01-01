<?php namespace App\Middleware;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FastRouteMiddleware
{
    private $dispatcher;
    
    public function __construct(array $routes)
    {
        $this->dispatcher = \FastRoute\simpleDispatcher(
            function (RouteCollector $r) use ($routes) {
                foreach ($routes as $route) {
                    $r->addRoute(
                        $route['method'],
                        $route['path'],
                        $route['handler']
                    );
                }
            }
        );
    }
    
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $routeInfo = $this->dispatcher->dispatch(
            $request->getMethod(),
            rawurldecode($request->getUri()->getPath())
        );
        
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $response = $response->withStatus(404);
                break;
            
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $response = $response
                    ->withStatus(405)
                    ->withHeader('Allow', $allowedMethods);
                break;
            
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $args = $routeInfo[2];
                $response = $handler($request, $response, $args);
                break;
        }
        
        return $next($request, $response);
    }
}

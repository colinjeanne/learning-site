<?php namespace App;

use League\Container\Container;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;
use Zend\Diactoros\Response;

class App
{
    private $container;
    
    public function __construct()
    {
        date_default_timezone_set('UTC');
        
        $this->container = new Container();
        
        $this->container->addServiceProvider(
            ServiceProviders\AuthServiceProvider::class
        );
        
        $this->container->addServiceProvider(
            ServiceProviders\DoctrineServiceProvider::class
        );
        
        $this->container->addServiceProvider(
            ServiceProviders\MonologServiceProvider::class
        );
        
        $this->container->addServiceProvider(
            ServiceProviders\RouteServiceProvider::class
        );
    }
    
    public function run(ServerRequestInterface $request)
    {
        $router = $this->container->get(Http\Router::class);
        $logger = $this->container->get(\Psr\Log\LoggerInterface::class);
        $jwtAuthorizer = $this->container->get(Auth\JwtAuthorizer::class);
        
        $relay = new RelayBuilder();
        $dispatcher = $relay->newInstance([
            new Middleware\RequestLoggerMiddleware($logger),
            new Middleware\ExceptionHandlerMiddleware($logger),
            new Middleware\AuthenticationMiddleware(
                $logger,
                $this->container->get('doctrine'),
                $jwtAuthorizer
            ),
            new Middleware\FastRouteMiddleware($router->getRoutes()),
        ]);
        
        return $dispatcher($request, new Response());
    }
}

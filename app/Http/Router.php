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
        
        $this->container->add(Controllers\ActivityController::class)
            ->withArgument('doctrine');
            
        $this->container->add(Controllers\UserController::class)
            ->withArgument('doctrine');
        
        $this->container->add(Controllers\FamilyController::class)
            ->withArgument('doctrine');
        
        $this->container->add(Controllers\InvitationsController::class)
            ->withArgument('doctrine');
        
        $this->container->add(Controllers\RootController::class);
        
        $this->addRoute('GET', '/', 'RootController@getIndex');
        
        $this->addRoute('GET', '/me', 'UserController@getMe');
        
        $this->addRoute(
            'GET',
            '/users/{id:\d+}',
            'UserController@getUser'
        );
        
        $this->addRoute(
            'PUT',
            '/users/{id:\d+}',
            'UserController@updateUser'
        );
        
        $this->addRoute('GET', '/me/family', 'FamilyController@getMyFamily');
        
        $this->addRoute(
            'POST',
            '/me/family/children',
            'FamilyController@addChild'
        );
        
        $this->addRoute(
            'PUT',
            '/me/family/children/{id:\d+}',
            'FamilyController@updateChild'
        );
        
        $this->addRoute(
            'GET',
            '/me/family/activities',
            'ActivityController@getActivities'
        );
        
        $this->addRoute(
            'POST',
            '/me/family/activities',
            'ActivityController@createActivity'
        );
        
        $this->addRoute(
            'GET',
            '/me/family/activities/{id:\d+}',
            'ActivityController@getActivity'
        );
        
        $this->addRoute(
            'PUT',
            '/me/family/activities/{id:\d+}',
            'ActivityController@updateActivity'
        );
        
        $this->addRoute(
            'GET',
            '/me/invitations',
            'InvitationsController@getInvitations'
        );
        
        $this->addRoute(
            'POST',
            '/me/invitations',
            'InvitationsController@inviteFamilyMember'
        );
        
        $this->addRoute(
            'POST',
            '/me/invitations/{id:[A-Fa-f\d]{8}-(?:[A-Fa-f\d]{4}-){3}[A-Fa-f\d]{12}}',
            'InvitationsController@acceptInvitation'
        );
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
                callable $next
            ) use (
                $className,
                $methodName
            ) {
                $controller = $this->container->get($className);
                
                $middleware = [];
                if (method_exists($controller, 'getMiddleware')) {
                    $middleware = $controller->getMiddleware($methodName);
                }
                
                $middleware[] = [$controller, $methodName];
                
                $relay = new RelayBuilder();
                $dispatcher = $relay->newInstance($middleware);
                $response = $dispatcher($request, $response);
                return $next($request, $response);
            };
        }
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
}

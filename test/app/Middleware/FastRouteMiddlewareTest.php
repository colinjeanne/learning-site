<?php namespace Test\Middleware;

use App\Middleware\FastRouteMiddleware;
use Test\Middleware\Utilities\RouteHandler;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Uri;

class FastRouteMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $response;
    
    public function setUp()
    {
        $this->response = new Response();
    }
    
    public function testNoRoutes()
    {
        $request = self::createRequest('GET', '/');
        
        $middleware = new FastRouteMiddleware([]);
        
        $response = $middleware(
            $request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );
        
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testUnknownRoute()
    {
        $rootHandler = new RouteHandler();
        $otherHandler = new RouteHandler();
        $routes = [
            [
                'method' => 'GET',
                'path' => '/',
                'handler' => $rootHandler
            ],
            [
                'method' => 'GET',
                'path' => '/known',
                'handler' => $otherHandler
            ]
        ];
        
        $request = self::createRequest('GET', '/unknown');
        
        $middleware = new FastRouteMiddleware($routes);
        
        $response = $middleware(
            $request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );
        
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($rootHandler->isHandled());
        $this->assertFalse($otherHandler->isHandled());
    }
    
    public function testKnownRouteWithoutArguments()
    {
        $rootHandler = new RouteHandler();
        $otherHandler = new RouteHandler();
        $routes = [
            [
                'method' => 'GET',
                'path' => '/',
                'handler' => $rootHandler
            ],
            [
                'method' => 'GET',
                'path' => '/known',
                'handler' => $otherHandler
            ]
        ];
        
        $request = self::createRequest('GET', '/known');
        
        $middleware = new FastRouteMiddleware($routes);
        
        $response = $middleware(
            $request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertFalse($rootHandler->isHandled());
        $this->assertTrue($otherHandler->isHandled());
        $this->assertEquals([], $otherHandler->getArguments());
    }
    
    public function testKnownRouteWithArguments()
    {
        $rootHandler = new RouteHandler();
        $otherHandler = new RouteHandler();
        $routes = [
            [
                'method' => 'GET',
                'path' => '/',
                'handler' => $rootHandler
            ],
            [
                'method' => 'GET',
                'path' => '/{id:\d+}/known',
                'handler' => $otherHandler
            ]
        ];
        
        $request = self::createRequest('GET', '/23/known');
        
        $middleware = new FastRouteMiddleware($routes);
        
        $response = $middleware(
            $request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertFalse($rootHandler->isHandled());
        $this->assertTrue($otherHandler->isHandled());
        $this->assertEquals(['id' => 23], $otherHandler->getArguments());
    }
    
    public function testMethodNotAllowed()
    {
        $rootHandler = new RouteHandler();
        $routes = [
            [
                'method' => 'GET',
                'path' => '/',
                'handler' => $rootHandler
            ],
            [
                'method' => 'PUT',
                'path' => '/',
                'handler' => $rootHandler
            ]
        ];
        
        $request = self::createRequest('POST', '/');
        
        $middleware = new FastRouteMiddleware($routes);
        
        $response = $middleware(
            $request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );
        
        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals(['GET', 'PUT'], $response->getHeader('Allow'));
        $this->assertFalse($rootHandler->isHandled());
    }
    
    private static function createRequest($method, $relativeUri)
    {
        return ServerRequestFactory::fromGlobals([], [], [], [], [])
            ->withMethod($method)
            ->withUri(new Uri('http://example.com' . $relativeUri));
    }
}

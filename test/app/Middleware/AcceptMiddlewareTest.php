<?php namespace Test\Middleware;

use App\Middleware\AcceptMiddleware;
use Test\Middleware\Utilities\TestHandler;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Uri;

class AcceptMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $response;
    
    public function setUp()
    {
        $this->response = new Response();
    }
    
    public function testMissingAcceptHeader()
    {
        $acceptableTypes = [
            'application/json'
        ];
        
        $middleware = new AcceptMiddleware($acceptableTypes);
        $handler = new TestHandler();
        
        $request = self::createRequest();
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($handler->isHandled());
        $this->assertEquals(
            'application/json',
            $handler->getMediaType()->getValue()
        );
    }
    
    public function testEmptyAcceptHeader()
    {
        $acceptableTypes = [
            'application/json'
        ];
        
        $middleware = new AcceptMiddleware($acceptableTypes);
        $handler = new TestHandler();
        
        $request = self::createRequest('');
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($handler->isHandled());
        $this->assertEquals(
            'application/json',
            $handler->getMediaType()->getValue()
        );
    }
    
    public function testInvalidAcceptHeader()
    {
        $acceptableTypes = [
            'application/json'
        ];
        
        $middleware = new AcceptMiddleware($acceptableTypes);
        $handler = new TestHandler();
        
        $request = self::createRequest('q=0.9, text');
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(406, $response->getStatusCode());
        $this->assertFalse($handler->isHandled());
    }
    
    public function testNoAcceptableTypes()
    {
        $acceptableTypes = [
            'application/json'
        ];
        
        $middleware = new AcceptMiddleware($acceptableTypes);
        $handler = new TestHandler();
        
        $request = self::createRequest('text/plain');
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(406, $response->getStatusCode());
        $this->assertFalse($handler->isHandled());
    }
    
    public function testOneAcceptableType()
    {
        $acceptableTypes = [
            'application/json',
            'text/plain'
        ];
        
        $middleware = new AcceptMiddleware($acceptableTypes);
        $handler = new TestHandler();
        
        $request = self::createRequest('application/json; text/html');
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($handler->isHandled());
        $this->assertEquals(
            'application/json',
            $handler->getMediaType()->getValue()
        );
    }
    
    public function testMultipleAcceptableTypes()
    {
        $acceptableTypes = [
            'application/json',
            'text/plain'
        ];
        
        $middleware = new AcceptMiddleware($acceptableTypes);
        $handler = new TestHandler();
        
        $request = self::createRequest('application/json;q=0.9, text/plain');
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($handler->isHandled());
        $this->assertEquals(
            'text/plain',
            $handler->getMediaType()->getValue()
        );
    }
    
    private static function createRequest($acceptHeader = null)
    {
        $request = ServerRequestFactory::fromGlobals([], [], [], [], [])
            ->withMethod('GET')
            ->withUri(new Uri('http://example.com/'));
        
        if ($acceptHeader !== null) {
            $request = $request->withHeader(
                'Accept',
                $acceptHeader
            );
        }
        
        return $request;
    }
}

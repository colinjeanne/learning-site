<?php namespace Test\Middleware;

use App\Middleware\ParseAsJsonMiddleware;
use Test\Middleware\Utilities\TestHandler;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class ParseAsJsonMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $response;
    
    public function setUp()
    {
        $this->response = new Response();
    }
    
    public function testMissingContentTypeHeader()
    {
        $middleware = new ParseAsJsonMiddleware();
        $handler = new TestHandler();
        
        $request = self::createRequest();
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(415, $response->getStatusCode());
        $this->assertFalse($handler->isHandled());
    }
    
    public function testNoJsonContentType()
    {
        $middleware = new ParseAsJsonMiddleware();
        $handler = new TestHandler();
        
        $request = self::createRequest('text/plain', '[]');
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(415, $response->getStatusCode());
        $this->assertFalse($handler->isHandled());
    }
    
    public function testInvalidJson()
    {
        $middleware = new ParseAsJsonMiddleware();
        $handler = new TestHandler();
        
        $request = self::createRequest('application/json', 'bad');
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(415, $response->getStatusCode());
        $this->assertFalse($handler->isHandled());
    }
    
    public function testJsonContentType()
    {
        $middleware = new ParseAsJsonMiddleware();
        $handler = new TestHandler();
        
        $request = self::createRequest('application/json', '[1, 2, 3]');
        $response = $middleware(
            $request,
            $this->response,
            $handler
        );
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($handler->isHandled());
        $this->assertEquals(
            [1, 2, 3],
            $handler->getRequest()->getParsedBody()
        );
    }
    
    private static function createRequest($contentType = null, $body = null)
    {
        $request = ServerRequestFactory::fromGlobals([], [], [], [], [])
            ->withMethod('GET')
            ->withUri(new Uri('http://example.com/'));
        
        if ($contentType !== null) {
            $request = $request->withHeader(
                'Content-Type',
                $contentType
            );
        }
        
        if ($body !== null) {
            $stream = new Stream('php://memory', 'rw');
            $stream->write($body);
            $request = $request->withBody($stream);
        }
        
        return $request;
    }
}

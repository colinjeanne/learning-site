<?php namespace Test\Middleware;

use App\Middleware\RequestLoggerMiddleware;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class RequestLoggerMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $request;
    private $response;
    
    public static function setupBeforeClass()
    {
        date_default_timezone_set('UTC');
    }
    
    public function setUp()
    {
        $this->request = ServerRequestFactory::fromGlobals([], [], [], [], []);
        $this->response = new Response();
    }
    
    public function testLogsStartAndEnd()
    {
        $testHandler = new TestHandler();
        $middleware = new RequestLoggerMiddleware(
            new Logger('test', [$testHandler])
        );
        
        $response = $middleware(
            $this->request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );
        
        $this->assertTrue($testHandler->hasInfoThatContains('Start Request'));
        $this->assertTrue($testHandler->hasInfoThatContains('End Request'));
    }
}

<?php namespace Test\Middleware;

use App\Middleware\ExceptionHandlerMiddleware;
use Psr\Log\NullLogger;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class ExceptionHandlerMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $request;
    private $response;
    
    public function setUp()
    {
        $this->request = ServerRequestFactory::fromGlobals([], [], [], [], []);
        $this->response = new Response();
    }
    
    public function testNoProblem()
    {
        $middleware = new ExceptionHandlerMiddleware(new NullLogger());
        
        $response = $middleware(
            $this->request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );
        
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testExceptionThrown()
    {
        $middleware = new ExceptionHandlerMiddleware(new NullLogger());
        
        $response = $middleware(
            $this->request,
            $this->response,
            function ($request, $response) {
                throw new \Exception();
            }
        );
        
        $this->assertEquals(500, $response->getStatusCode());
    }
    
    /**
     * @dataProvider errors
     */
    public function testErrorTriggered($error)
    {
        $middleware = new ExceptionHandlerMiddleware(new NullLogger());
        
        $response = $middleware(
            $this->request,
            $this->response,
            function ($request, $response) {
                trigger_error('Error', $error);
                return $response;
            }
        );
        
        $this->assertEquals(500, $response->getStatusCode());
    }
    
    public function errors()
    {
        $errors = [
            E_ERROR,
            E_WARNING,
            E_NOTICE,
            E_USER_ERROR,
            E_USER_WARNING,
            E_USER_NOTICE,
            E_STRICT,
            E_RECOVERABLE_ERROR,
            E_DEPRECATED,
            E_USER_DEPRECATED
        ];
        
        foreach ($errors as $error) {
            yield [$error];
        }
    }
}

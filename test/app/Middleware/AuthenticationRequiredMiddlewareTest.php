<?php namespace Test\Middleware;

use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationRequiredMiddleware;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Uri;

class AuthenticationRequiredMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $response;

    public function setUp()
    {
        $this->response = new Response();
    }

    public function testHasCurrentUser()
    {
        $middleware = new AuthenticationRequiredMiddleware();

        $request = self::createRequest([]);
        $response = $middleware(
            $request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNoCurrentUser()
    {
        $middleware = new AuthenticationRequiredMiddleware();

        $request = self::createRequest();
        $response = $middleware(
            $request,
            $this->response,
            function ($request, $response) {
                return $response;
            }
        );

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('WWW-Authenticate'));
        $this->assertEquals(
            'Bearer',
            $response->getHeaderLine('WWW-Authenticate')
        );
    }

    private static function createRequest($currentUser = null)
    {
        $request = ServerRequestFactory::fromGlobals([], [], [], [], [])
            ->withMethod('GET')
            ->withUri(new Uri('http://example.com/'));

        if ($currentUser !== null) {
            $request = $request->withAttribute(
                \App\Auth\Constants::CURRENT_USER_KEY,
                $currentUser
            );
        }

        return $request;
    }
}

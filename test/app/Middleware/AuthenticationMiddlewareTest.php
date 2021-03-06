<?php namespace Test\Middleware;

use App\Middleware\AuthenticationMiddleware;
use Psr\Log\NullLogger;
use Test\Middleware\Utilities\TestHandler;
use Test\Middleware\Utilities\TestJwtAuthorizer;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Uri;

class AuthenticationMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $response;

    public static function setupBeforeClass()
    {
        date_default_timezone_set('UTC');
    }

    public function setUp()
    {
        $this->response = new Response();
    }

    public function testNoAuthorizationHeader()
    {
        $claims = [];
        $request = self::createRequest();
        $db = $this->createDatabase(true);
        $testHandler = new TestHandler();
        $middleware = new AuthenticationMiddleware(
            new NullLogger(),
            $db,
            new TestJwtAuthorizer($claims)
        );

        $response = $middleware(
            $request,
            $this->response,
            $testHandler
        );

        $this->assertTrue($testHandler->isHandled());
        $this->assertNull($testHandler->getCurrentUser());
    }

    public function testNonBearerAuthorization()
    {
        $claims = [];
        $testHandler = new TestHandler();
        $request = self::createRequest('Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==');
        $db = $this->createDatabase(true);
        $middleware = new AuthenticationMiddleware(
            new NullLogger(),
            $db,
            new TestJwtAuthorizer($claims)
        );

        $response = $middleware(
            $request,
            $this->response,
            $testHandler
        );

        $this->assertTrue($testHandler->isHandled());
        $this->assertNull($testHandler->getCurrentUser());
    }

    public function testBearerMissingToken()
    {
        $claims = [];
        $testHandler = new TestHandler();
        $request = self::createRequest('Bearer');
        $db = $this->createDatabase(true);
        $middleware = new AuthenticationMiddleware(
            new NullLogger(),
            $db,
            new TestJwtAuthorizer($claims)
        );

        $response = $middleware(
            $request,
            $this->response,
            $testHandler
        );

        $this->assertTrue($testHandler->isHandled());
        $this->assertNull($testHandler->getCurrentUser());
    }

    public function testInvalidToken()
    {
        // HMAC256 with iss and sub in the payload
        $token =
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJleGFtcGxlLmNvb' .
            'SIsInN1YiI6IjEifQ.1eL1FdhT4yJSi1a-cxjQk76S4_ngqg-mARM48A7SoM8';

        $claims = [
            'iss' => 'example.com',
            'sub' => '1'
        ];

        $testHandler = new TestHandler();
        $request = self::createRequest('Bearer QWxhZGRpbjpvcGVuIHNlc2FtZQ==');
        $db = $this->createDatabase(true);
        $testAuthorizer = new TestJwtAuthorizer($claims);
        $testAuthorizer->setShouldFailGetClaims(true);
        $middleware = new AuthenticationMiddleware(
            new NullLogger(),
            $db,
            $testAuthorizer
        );

        $response = $middleware(
            $request,
            $this->response,
            $testHandler
        );

        $this->assertTrue($testHandler->isHandled());
        $this->assertNull($testHandler->getCurrentUser());
    }

    public function testTokenMissingIssuer()
    {
        // HMAC256 with only sub in the payload
        $token =
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxIn0.8qZF8vbN3U' .
            'pcanXFc-mPXJkOPN01-bRch8XX3rToP1U';

        $claims = [
            'sub' => '1'
        ];

        $testHandler = new TestHandler();
        $request = self::createRequest('Bearer ' . $token);
        $db = $this->createDatabase(true);
        $middleware = new AuthenticationMiddleware(
            new NullLogger(),
            $db,
            new TestJwtAuthorizer($claims)
        );

        $response = $middleware(
            $request,
            $this->response,
            $testHandler
        );

        $this->assertTrue($testHandler->isHandled());
        $this->assertNull($testHandler->getCurrentUser());
    }

    public function testTokenMissingSubject()
    {
        // HMAC256 with only iss in the payload
        $token =
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJleGFtcGxlLmNvbS' .
            'J9.c2lmFOiVCSRyegrYJjx60BzBhacHt3BZ-avr4PtGqWk';

        $claims = [
            'iss' => 'example.com'
        ];

        $testHandler = new TestHandler();
        $request = self::createRequest('Bearer ' . $token);
        $db = $this->createDatabase(true);
        $middleware = new AuthenticationMiddleware(
            new NullLogger(),
            $db,
            new TestJwtAuthorizer($claims)
        );

        $response = $middleware(
            $request,
            $this->response,
            $testHandler
        );

        $this->assertTrue($testHandler->isHandled());
        $this->assertNull($testHandler->getCurrentUser());
    }

    public function testNoUserFound()
    {
        // HMAC256 with iss and sub in the payload
        $token =
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJleGFtcGxlLmNvb' .
            'SIsInN1YiI6IjEifQ.1eL1FdhT4yJSi1a-cxjQk76S4_ngqg-mARM48A7SoM8';

        $claims = [
            'iss' => 'example.com',
            'sub' => '1'
        ];

        $testHandler = new TestHandler();
        $request = self::createRequest('Bearer ' . $token);
        $db = $this->createDatabase(false);
        $middleware = new AuthenticationMiddleware(
            new NullLogger(),
            $db,
            new TestJwtAuthorizer($claims)
        );

        $response = $middleware(
            $request,
            $this->response,
            $testHandler
        );

        $this->assertTrue($testHandler->isHandled());
        $this->assertNull($testHandler->getCurrentUser());
    }

    public function testUserFound()
    {
        // HMAC256 with iss and sub in the payload
        $token =
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJleGFtcGxlLmNvb' .
            'SIsInN1YiI6IjEifQ.1eL1FdhT4yJSi1a-cxjQk76S4_ngqg-mARM48A7SoM8';

        $claims = [
            'iss' => 'example.com',
            'sub' => '1'
        ];

        $testHandler = new TestHandler();
        $request = self::createRequest('Bearer ' . $token);
        $db = $this->createDatabase(true);
        $middleware = new AuthenticationMiddleware(
            new NullLogger(),
            $db,
            new TestJwtAuthorizer($claims)
        );

        $response = $middleware(
            $request,
            $this->response,
            $testHandler
        );

        $this->assertTrue($testHandler->isHandled());
        $this->assertNotNull($testHandler->getCurrentUser());
    }

    private function createDatabase($hasUser)
    {
        $userRepository = $this->getMockBuilder(
            \App\Models\UserRepository::class
        )
            ->disableOriginalConstructor()
            ->getMock();

        $claim = new \App\Models\Claim('iss', 'sub');

        if ($hasUser) {
            $user = new \App\Models\User();
            $claim->setUser($user);

            $userRepository->method('findByIssuerAndSubject')
                ->willReturn($user);
        }

        $db = $this->getMockBuilder(
            \Doctrine\Common\Persistence\ObjectManager::class
        )->getMock();

        $db->method('getRepository')
            ->willReturn($userRepository);

        return $db;
    }

    private static function createRequest($authenticationToken = null)
    {
        $request = ServerRequestFactory::fromGlobals([], [], [], [], [])
            ->withMethod('GET')
            ->withUri(new Uri('http://example.com/'));

        if ($authenticationToken !== null) {
            $request = $request->withHeader(
                'Authorization',
                $authenticationToken
            );
        }

        return $request;
    }
}

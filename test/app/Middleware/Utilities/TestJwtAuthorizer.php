<?php namespace Test\Middleware\Utilities;

use App\Auth\JwtAuthorizer;
use Psr\Http\Message\ServerRequestInterface;

class TestJwtAuthorizer implements JwtAuthorizer
{
    private $claims;
    private $shouldFailGetClaims = false;

    public function __construct($claims)
    {
        $this->claims = $claims;
    }

    public function getClaims($jwt)
    {
        if ($this->shouldFailGetClaims) {
            throw new \Exception('Failing getClaims');
        }

        return $this->claims;
    }

    public function setShouldFailGetClaims($shouldFailGetClaims)
    {
        $this->shouldFailGetClaims = true;
    }

    public function isOAuthRedirect(ServerRequestInterface $request)
    {
        return false;
    }

    public function getOAuthUri(ServerRequestInterface $request)
    {
        return '';
    }

    public function hasValidOAuthCode(ServerRequestInterface $request)
    {
        return false;
    }

    public function getIdToken()
    {
        return '';
    }
}

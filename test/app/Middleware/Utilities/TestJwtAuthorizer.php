<?php namespace Test\Middleware\Utilities;

use App\Auth\JwtAuthorizer;

class TestJwtAuthorizer implements JwtAuthorizer
{
    private $claims;
    
    public function __construct($claims)
    {
        $this->claims = $claims;
    }
    
    public function getClaims($jwt)
    {
        return $this->claims;
    }
}

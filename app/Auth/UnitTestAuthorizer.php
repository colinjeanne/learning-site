<?php namespace App\Auth;

class UnitTestAuthorizer implements JwtAuthorizer
{
    public function getClaims($jwt)
    {
        return [
            'iss' => getenv('CLAIM_ISSUER'),
            'sub' => getenv('CLAIM_SUBJECT')
        ];
    }
}

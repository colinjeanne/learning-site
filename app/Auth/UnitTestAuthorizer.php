<?php namespace App\Auth;

use Psr\Http\Message\ServerRequestInterface;

class UnitTestAuthorizer implements JwtAuthorizer
{
    public function getClaims($jwt)
    {
        return [
            'iss' => getenv('CLAIM_ISSUER'),
            'sub' => getenv('CLAIM_SUBJECT')
        ];
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

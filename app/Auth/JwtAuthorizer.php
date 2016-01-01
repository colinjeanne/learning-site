<?php namespace App\Auth;

interface JwtAuthorizer
{
    /**
     * Gets the claims from the JSON web token.
     *
     * @param string $jwt A JSON web token
     * @return array An array containing the claims of the JSON web token
     */
    public function getClaims($jwt);
}

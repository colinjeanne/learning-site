<?php namespace App\Auth;

use Psr\Http\Message\ServerRequestInterface;

interface JwtAuthorizer
{
    /**
     * Gets the claims from the JSON web token.
     *
     * @param string $jwt A JSON web token
     * @return array An array containing the claims of the JSON web token
     */
    public function getClaims($jwt);

    /**
     * Determines if the request is the final step of an OAuth sequence.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request The request
     * @return bool
     */
    public function isOAuthRedirect(ServerRequestInterface $request);

    /**
     * Gets the URI to redirect the user to in order to start the OAuth
     * sequence.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request The request
     * @return string The redirect URI.
     */
    public function getOAuthUri(ServerRequestInterface $request);

    /**
     * Determines if the request has a valid OAuth authorization code.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request The request
     * @return bool
     */
    public function hasValidOAuthCode(ServerRequestInterface $request);

    /**
     * Gets the ID token.
     *
     * @return string The ID token.
     */
    public function getIdToken();
}

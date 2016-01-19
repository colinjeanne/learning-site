<?php namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationRequiredMiddleware
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        if ($currentUser === null) {
            return $response
                ->withStatus(401)
                ->withHeader('WWW-Authenticate', 'Bearer');
        }
        
        return $next($request, $response);
    }
}

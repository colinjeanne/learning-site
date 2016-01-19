<?php namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ParseAsJsonMiddleware
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $contentType = $request->getHeaderLine('Content-Type');
        
        if ($contentType !== 'application/json') {
            return $response->withStatus(415);
        }
        
        $body = (string)$request->getBody();
        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $response->withStatus(415);
        }
        
        $request = $request->withParsedBody($json);
        
        return $next($request, $response);
    }
}

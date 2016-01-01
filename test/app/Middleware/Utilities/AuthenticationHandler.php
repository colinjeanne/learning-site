<?php namespace Test\Middleware\Utilities;

use App\Middleware\AuthenticationMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationHandler
{
    private $handled = false;
    private $currentUser;
    
    public function isHandled()
    {
        return $this->handled;
    }
    
    public function getCurrentUser()
    {
        return $this->currentUser;
    }
    
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->handled = true;
        $this->currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        return $response;
    }
}

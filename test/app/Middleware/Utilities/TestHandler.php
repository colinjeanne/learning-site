<?php namespace Test\Middleware\Utilities;

use App\Middleware\AcceptMiddleware;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\FastRouteMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestHandler
{
    private $handled = false;
    private $request;
    
    public function isHandled()
    {
        return $this->handled;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getArguments()
    {
        return $this->request->getAttribute(
            FastRouteMiddleware::ROUTE_ARGUMENTS
        );
    }
    
    public function getMediaType()
    {
        return $this->request->getAttribute(
            AcceptMiddleware::ACCEPT_MEDIA_TYPE
        );
    }
    
    public function getCurrentUser()
    {
        return $this->request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
    }
    
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->handled = true;
        $this->request = $request;
        
        return $response;
    }
}

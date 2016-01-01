<?php namespace Test\Middleware\Utilities;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouteHandler
{
    private $handled = false;
    private $args = [];
    
    public function isHandled()
    {
        return $this->handled;
    }
    
    public function getArguments()
    {
        return $this->args;
    }
    
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ) {
        $this->handled = true;
        $this->args = $args;
        return $response;
    }
}

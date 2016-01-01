<?php namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class RequestLoggerMiddleware
{
    private $log;
    
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }
    
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $start = microtime(true);
        $this->log->info(
            'Start Request',
            [
                'method' => $request->getMethod(),
                'target' => $request->getRequestTarget(),
                'time' => $start
            ]
        );
        
        $response = $next($request, $response);
        
        $this->log->info(
            'End Request',
            [
                'duration' => microtime(true) - $start,
                'status' => $response->getStatusCode()
            ]
        );
        
        return $response;
    }
}

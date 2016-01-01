<?php namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

function handleError($severity, $message, $file, $line)
{
    if (!(error_reporting() & $severity)) {
        return;
    }
    
    throw new \ErrorException($message, 0, $severity, $file, $line);
}

class ExceptionHandlerMiddleware
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
        $oldHandler = set_error_handler('\App\Middleware\handleError');
        
        try {
            $response = $next($request, $response);
        } catch (\Exception $e) {
            $this->log->error('Unhandled Exception', ['exception' => $e]);
            $response = $response->withStatus(500);
        }
        
        set_error_handler($oldHandler);
        
        return $response;
    }
}

<?php namespace App\Middleware;

use Negotiation\Negotiator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class AcceptMiddleware
{
    const ACCEPT_MEDIA_TYPE = 'accept_mime_type';
    
    private $acceptTypes;
    private $negotiator;
    
    public function __construct(array $acceptTypes)
    {
        $this->acceptTypes = $acceptTypes;
        $this->negotiator = new Negotiator();
    }
    
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $acceptHeader = $request->getHeaderLine('Accept');
        if ($acceptHeader === '') {
            $acceptHeader = '*/*';
        }
        
        try {
            $mediaType = $this->negotiator->getBest(
                $acceptHeader,
                $this->acceptTypes
            );
        } catch (\Negotiation\Exception\Exception $e) {
            $mediaType = null;
        }
        
        if (!$mediaType) {
            return new JsonResponse(
                $this->acceptTypes,
                406,
                $response->getHeaders()
            );
        }
        
        $request = $request->withAttribute(
            self::ACCEPT_MEDIA_TYPE,
            $mediaType
        );
        
        return $next($request, $response);
    }
}

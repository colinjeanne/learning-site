<?php namespace App\Http\Controllers;

use App\Middleware\AcceptMiddleware;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationRequiredMiddleware;
use App\Models\Family;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

require_once 'Common.php';

function familyToJson(ServerRequestInterface $request, Family $family)
{
    return [
        'members' => array_map(
            function (User $user) use ($request) {
            
                return getUserUri($request, $user);
            },
            $family->getMembers()->getValues()
        ),
    ];
}

class FamilyController
{
    private $db;
    
    public function __construct(ObjectManager $db)
    {
        $this->db = $db;
    }
    
    public function getMiddleware($methodName)
    {
        $middleware = [
            new AuthenticationRequiredMiddleware(),
            new AcceptMiddleware(['application/json']),
            createEnsureCurrentUserFamilyMiddleware($this->db)
        ];
        
        switch ($methodName) {
            case 'getMyFamily':
                break;
        }
        
        return $middleware;
    }
    
    public function getMyFamily(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        return new JsonResponse(
            familyToJson($request, $currentUser->getFamily()),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
}

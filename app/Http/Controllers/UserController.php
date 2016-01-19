<?php namespace App\Http\Controllers;

use App\Middleware\AcceptMiddleware;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationRequiredMiddleware;
use App\Middleware\FastRouteMiddleware;
use App\Middleware\ParseAsJsonMiddleware;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface as NestedValidationException;
use Respect\Validation\Validator as v;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

require_once 'Common.php';

function areUsersFamily(User $userA, User $userB)
{
    return ($userA->getId() === $userB->getId()) ||
        ($userA->getFamily() && $userB->getFamily() &&
         ($userA->getFamily()->getId() === $userB->getFamily()->getId()));
}

function createReadUserArgumentsMiddleware(ObjectManager $db)
{
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) use (
        $db
    ) {
        $arguments = $request->getAttribute(
            FastRouteMiddleware::ROUTE_ARGUMENTS
        );
        
        $userId = $arguments['id'];
        $user = $db->getRepository(User::class)->find($userId);
        if (!$user) {
            return new EmptyResponse(
                404,
                $response->getHeaders()
            );
        }
        
        $request = $request->withAttribute(READ_USER_KEY, $user);
        
        return $next($request, $response);
    };
}

function validateUserJsonForUpdate(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $user = $request->getAttribute(READ_USER_KEY);
    $json = $request->getParsedBody();
    
    $familyUri = (string)$request
        ->getUri()
        ->withPath('/me/family');
    
    $invitationsUri = (string)$request
        ->getUri()
        ->withPath('/me/invitations');
    
    $validator = v::arrType()->
        keyset(
            v::key('name', v::strType()->length(0, 255)),
            v::key(
                'links',
                v::arrType()->keySet(
                    v::key('self', v::equals(getUserUri($request, $user))),
                    v::key('family', v::equals($familyUri), false),
                    v::key('invitations', v::equals($invitationsUri), false)
                ),
                false
            )
        );
    
    try {
        $validator->assert($json);
    } catch (NestedValidationException $e) {
        return new JsonResponse(
            $e->getFullMessage(),
            400,
            $response->getHeaders()
        );
    }
    
    return $next($request, $response);
}

function authorizeCurrentUserToReadUser(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $user = $request->getAttribute(READ_USER_KEY);
    
    $currentUser = $request->getAttribute(
        AuthenticationMiddleware::CURRENT_USER_KEY
    );
    
    if (!areUsersFamily($currentUser, $user)) {
        return new EmptyResponse(
            403,
            $response->getHeaders()
        );
    }
    
    return $next($request, $response);
}

function authorizeCurrentUserToUpdateUser(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $user = $request->getAttribute(READ_USER_KEY);
    
    $currentUser = $request->getAttribute(
        AuthenticationMiddleware::CURRENT_USER_KEY
    );
    
    if (!$user->getId() !== $currentUser->getId()) {
        new EmptyResponse(
            403,
            $response->getHeaders()
        );
    }
    
    return $next($request, $response);
}

function userToJson(ServerRequestInterface $request, User $user)
{
    $json = [
        'name' => $user->getName(),
        'links' => [
            'self' => getUserUri($request, $user)
        ]
    ];
    
    $currentUser = $request->getAttribute(
        AuthenticationMiddleware::CURRENT_USER_KEY
    );
    
    if ($user->getId() === $currentUser->getId()) {
        $json['links']['family'] =
            (string)$request->getUri()->withPath('/me/family');
        $json['links']['invitations'] =
            (string)$request->getUri()->withPath('/me/invitations');
    }
    
    return $json;
}

function updateUserFromJson(User $user, array $json)
{
    $user->setName($json['name']);
}

class UserController
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
            new AcceptMiddleware(['application/json'])
        ];
        
        switch ($methodName) {
            case 'getMe':
                break;
            
            case 'getUser':
                $middleware[] = createReadUserArgumentsMiddleware($this->db);
                $middleware[] =
                    '\App\Http\Controllers\authorizeCurrentUserToReadUser';
                break;
            
            case 'updateUser':
                $middleware[] = new ParseAsJsonMiddleware();
                $middleware[] = createReadUserArgumentsMiddleware($this->db);
                $middleware[] =
                    '\App\Http\Controllers\validateUserJsonForUpdate';
                $middleware[] =
                    '\App\Http\Controllers\authorizeCurrentUserToReadUser';
                $middleware[] =
                    '\App\Http\Controllers\authorizeCurrentUserToUpdateUser';
                break;
        }
        
        return $middleware;
    }
    
    public function getMe(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        return new JsonResponse(
            userToJson($request, $currentUser),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
    
    public function getUser(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $user = $request->getAttribute(READ_USER_KEY);
        
        return new JsonResponse(
            userToJson($request, $user),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
    
    public function updateUser(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $user = $request->getAttribute(READ_USER_KEY);
        $json = $request->getParsedBody();
        
        updateUserFromJson($user, $json);
        
        $this->db->flush();
        
        return new JsonResponse(
            userToJson($request, $user),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
}
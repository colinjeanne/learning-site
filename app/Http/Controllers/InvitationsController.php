<?php namespace App\Http\Controllers;

use App\Middleware\AcceptMiddleware;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationRequiredMiddleware;
use App\Middleware\FastRouteMiddleware;
use App\Middleware\ParseAsJsonMiddleware;
use App\Models\FamilyInvitation;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface as NestedValidationException;
use Respect\Validation\Validator as v;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

require_once __DIR__ . '/Common.php';

const READ_INVITATION_KEY = 'read_invitation_key';

function createValidateUserIdMiddleware(ObjectManager $db)
{
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) use (
        $db
    ) {
        $json = $request->getParsedBody();
        
        $baseUsersUri = (string)$request
            ->getUri()
            ->withPath('/users/');
        
        $usersUriRegex = '/^' . preg_quote($baseUsersUri, '/') . '(\d+)$/';
        
        $validator = v::regex($usersUriRegex);
        
        try {
            $validator->assert($json);
        } catch (NestedValidationException $e) {
            return new JsonResponse(
                $e->getFullMessage(),
                400,
                $response->getHeaders()
            );
        }
        
        $matches = [];
        $user = null;
        if (preg_match($usersUriRegex, $json, $matches) === 1) {
            $userId = $matches[1];
            $user = $db->getRepository(User::class)->find($userId);
        }
        
        if (!$user) {
            return new JsonResponse(
                ['Invalid user Id'],
                400,
                $response->getHeaders()
            );
        }
        
        $request = $request->withAttribute(READ_USER_KEY, $user);
        
        return $next($request, $response);
    };
}

function createValidateFamilyInvitationMiddleware(ObjectManager $db)
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
        
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        $invitationId = $arguments['id'];
        $invitation =
            $db->getRepository(FamilyInvitation::class)
                ->findOneBy([
                    'id' => $invitationId,
                    'user' => $currentUser
                ]);
        
        if (!$invitation) {
            return new EmptyResponse(
                404,
                $response->getHeaders()
            );
        }
        
        $request = $request->withAttribute(READ_INVITATION_KEY, $invitation);
        
        return $next($request, $response);
    };
}

function getInvitationUri(
    ServerRequestInterface $request,
    FamilyInvitation $invitation
) {
    return (string)$request
        ->getUri()
        ->withPath('/me/invitations/' . $invitation->getId());
}

function invitationToJson(
    ServerRequestInterface $request,
    FamilyInvitation $invitation
) {
    return [
        'id' => getInvitationUri($request, $invitation),
        'createdBy' => getUserUri($request, $invitation->getCreatedBy())
    ];
}

class InvitationsController
{
    private $db;
    
    public function __construct(ObjectManager $db)
    {
        $this->db = $db;
    }
    
    public function getMiddleware($methodName)
    {
        $middleware = [
            new AuthenticationRequiredMiddleware()
        ];
        
        switch ($methodName) {
            case 'getInvitations':
                $middleware[] = new AcceptMiddleware(['application/json']);
                break;
            
            case 'inviteFamilyMember':
                $middleware[] = new ParseAsJsonMiddleware();
                $middleware[] = createValidateUserIdMiddleware($this->db);
                $middleware[] =
                    createEnsureCurrentUserFamilyMiddleware($this->db);
                break;
            
            case 'acceptInvitation':
                $middleware[] =
                    createValidateFamilyInvitationMiddleware($this->db);
                break;
        }
        
        return $middleware;
    }
    
    public function getInvitations(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        $json = array_map(
            function (FamilyInvitation $invitation) use ($request) {
            
                return invitationToJson($request, $invitation);
            },
            $currentUser->getInvitations()->getValues()
        );
        
        return new JsonResponse(
            $json,
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
    
    public function inviteFamilyMember(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        $family = $currentUser->getFamily();
        
        $user = $request->getAttribute(READ_USER_KEY);
        
        if (!$family->hasMember($user)) {
            if ($family->hasMaxMembers()) {
                return new JsonResponse(
                    ['Family has too many members'],
                    400,
                    $response->getHeaders()
                );
            }
            
            if (!$family->canInviteMembers()) {
                return new JsonResponse(
                    ['Family has too many outstanding invitations'],
                    400,
                    $response->getHeaders()
                );
            }
            
            if ($user->getFamily() && !$user->getFamily()->isEmpty()) {
                return new JsonResponse(
                    ['User is already in a family'],
                    400,
                    $response->getHeaders()
                );
            }
            
            $invitation = new FamilyInvitation($user, $currentUser);
            
            try {
                $this->db->persist($invitation);
                $this->db->flush();
            } catch (UniqueConstraintViolationException $e) {
                // Do nothing, the invitation already exists
            }
        }
        
        return new EmptyResponse(
            204,
            $response->getHeaders()
        );
    }
    
    public function acceptInvitation(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        $originalFamily = $currentUser->getFamily();
        $invitation = $request->getAttribute(READ_INVITATION_KEY);
        $family = $invitation->getFamily();
        
        if (!$family->hasMember($currentUser)) {
            if ($family->hasMaxMembers()) {
                return new JsonResponse(
                    ['Family has too many members'],
                    400,
                    $response->getHeaders()
                );
            }
            
            $family->addMember($currentUser);
        }
        
        foreach ($currentUser->getInvitations() as $oldInvitation) {
            $this->db->remove($oldInvitation);
        }
        
        if ($originalFamily) {
            $this->db->remove($originalFamily);
        }
        
        $this->db->flush();
        
        return new EmptyResponse(
            204,
            $response->getHeaders()
        );
    }
}

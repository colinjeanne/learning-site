<?php namespace App\Http\Controllers;

use App\Auth\Constants;
use App\Middleware\AcceptMiddleware;
use App\Middleware\AuthenticationRequiredMiddleware;
use App\Middleware\ParseAsJsonMiddleware;
use App\Models\Child;
use App\Models\Family;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

require_once __DIR__ . '/../../Assets/Skills.php';
require_once __DIR__ . '/Common.php';

const READ_CHILD_KEY = 'read_child_key';

function getChildUri(ServerRequestInterface $request, Child $child)
{
    return (string)$request
        ->getUri()
        ->withPath('/me/family/children/' . $child->getId());
}

function authorizeCurrentUserToUpdateChild(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $child = $request->getAttribute(READ_CHILD_KEY);

    $currentUser = $request->getAttribute(
        Constants::CURRENT_USER_KEY
    );

    if (!$currentUser->getFamily()->hasChild($child)) {
        return new EmptyResponse(
            403,
            $response->getHeaders()
        );
    }

    return $next($request, $response);
}

function childToJson(ServerRequestInterface $request, Child $child)
{
    return [
        'name' => $child->getName(),
        'skills' => $child->getSkills(),
        'links' => [
            'self' => getChildUri($request, $child)
        ]
    ];
}

function familyToJson(ServerRequestInterface $request, Family $family)
{
    return [
        'members' => array_map(
            function (User $user) use ($request) {
                return userToJson($request, $user);
            },
            $family->getMembers()->getValues()
        ),
        'children' => array_map(
            function (Child $child) use ($request) {
                return childToJson($request, $child);
            },
            $family->getChildren()->getValues()
        )
    ];
}

function validateChildJsonForCreate(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $json = $request->getParsedBody();

    $validator = v::arrayType()->
        keySet(
            v::key('name', v::stringType()->length(0, 255))
        );

    try {
        $validator->assert($json);
    } catch (NestedValidationException $e) {
        return new JsonResponse(
            [$e->getFullMessage()],
            400,
            $response->getHeaders()
        );
    }

    return $next($request, $response);
}

function validateChildJsonForUpdate(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $child = $request->getAttribute(READ_CHILD_KEY);
    $json = $request->getParsedBody();

    $validator = v::arrayType()->
        keySet(
            v::key('name', v::stringType()->length(0, 255)),
            v::key('skills', v::arrayType()),
            v::key(
                'links',
                v::arrayType()->keySet(
                    v::key('self', v::equals(getChildUri($request, $child)))
                ),
                false
            )
        );

    try {
        $validator->assert($json);
    } catch (NestedValidationException $e) {
        return new JsonResponse(
            [$e->getFullMessage()],
            400,
            $response->getHeaders()
        );
    } catch (ValidationException $e) {
        return new JsonResponse(
            [$e->getMainMessage()],
            400,
            $response->getHeaders()
        );
    }

    try {
        \App\Assets\validateSkills($json['skills']);
    } catch (\App\Assets\SkillValidationException $e) {
        return new JsonResponse(
            [$e->getMessage()],
            400,
            $response->getHeaders()
        );
    }

    return $next($request, $response);
}

function updateChildFromJson(Child $child, array $json)
{
    $child->setName($json['name']);
    $child->setSkills($json['skills']);
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

        $readChildMiddleware =
            \App\Http\Controllers\createReadObjectArgumentsMiddleware(
                $this->db,
                Child::class,
                READ_CHILD_KEY
            );

        switch ($methodName) {
            case 'getMyFamily':
                break;

            case 'addChild':
                $middleware[] = new ParseAsJsonMiddleware();
                $middleware[] =
                    '\App\Http\Controllers\validateChildJsonForCreate';
                break;

            case 'updateChild':
                $middleware[] = new ParseAsJsonMiddleware();
                $middleware[] = $readChildMiddleware;
                $middleware[] =
                    '\App\Http\Controllers\authorizeCurrentUserToUpdateChild';
                $middleware[] =
                    '\App\Http\Controllers\validateChildJsonForUpdate';
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
            Constants::CURRENT_USER_KEY
        );

        return new JsonResponse(
            familyToJson($request, $currentUser->getFamily()),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }

    public function addChild(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            Constants::CURRENT_USER_KEY
        );

        $family = $currentUser->getFamily();

        $json = $request->getParsedBody();
        $child = new Child($json['name']);

        if (!$family->hasChild($child)) {
            if ($family->hasMaxChildren()) {
                return new JsonResponse(
                    ['Family has too many children'],
                    400,
                    $response->getHeaders()
                );
            }

            $family->addChild($child);

            $this->db->persist($child);
            $this->db->flush();
        }

        return new JsonResponse(
            childToJson($request, $child),
            201,
            $response->getHeaders()
        );
    }

    public function updateChild(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            Constants::CURRENT_USER_KEY
        );

        $child = $request->getAttribute(READ_CHILD_KEY);
        $json = $request->getParsedBody();

        updateChildFromJson($child, $json);

        $this->db->flush();

        return new JsonResponse(
            childToJson($request, $child),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
}

<?php namespace App\Http\Controllers;

use App\Auth\Constants;
use App\Middleware\FastRouteMiddleware;
use App\Models\Family;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

const READ_USER_KEY = 'read_user_key';

function getUserUri(ServerRequestInterface $request, User $user)
{
    return (string)$request
        ->getUri()
        ->withPath('/users/' . $user->getId());
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
        Constants::CURRENT_USER_KEY
    );

    if ($user->getId() === $currentUser->getId()) {
        $json['links']['family'] =
            (string)$request->getUri()->withPath('/me/family');
        $json['links']['invitations'] =
            (string)$request->getUri()->withPath('/me/invitations');
    }

    return $json;
}

function createEnsureCurrentUserFamilyMiddleware(ObjectManager $db)
{
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) use (
        $db
    ) {
        $currentUser = $request->getAttribute(
            Constants::CURRENT_USER_KEY
        );

        if (!$currentUser->getFamily()) {
            $family = new Family();
            $family->addMember($currentUser);

            // Don't both flushing the database for this operation. If
            // something else flushes the database, that's fine but otherwise
            // we can simply regenerate this family if it didn't already exist.
            $db->persist($family);
        }

        return $next($request, $response);
    };
}

function createReadObjectArgumentsMiddleware(ObjectManager $db, $class, $key)
{
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) use (
        $db,
        $class,
        $key
    ) {
        $arguments = $request->getAttribute(
            FastRouteMiddleware::ROUTE_ARGUMENTS
        );

        $id = $arguments['id'];
        $obj = $db->getRepository($class)->find($id);
        if (!$obj) {
            return new EmptyResponse(
                404,
                $response->getHeaders()
            );
        }

        $request = $request->withAttribute($key, $obj);

        return $next($request, $response);
    };
}

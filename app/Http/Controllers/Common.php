<?php namespace App\Http\Controllers;

use App\Middleware\AuthenticationMiddleware;
use App\Models\Family;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

const READ_USER_KEY = 'read_user_key';

function getUserUri(ServerRequestInterface $request, User $user)
{
    return (string)$request
        ->getUri()
        ->withPath('/users/' . $user->getId());
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
            AuthenticationMiddleware::CURRENT_USER_KEY
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

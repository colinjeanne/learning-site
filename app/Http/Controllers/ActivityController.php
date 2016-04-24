<?php namespace App\Http\Controllers;

use App\Middleware\AcceptMiddleware;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationRequiredMiddleware;
use App\Middleware\ParseAsJsonMiddleware;
use App\Models\Activity;
use App\Models\ActivityLink;
use App\Models\Family;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface as NestedValidationException;
use Respect\Validation\Validator as v;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

require_once __DIR__ . '/Common.php';

const READ_ACTIVITY_KEY = 'read_activity_key';

function getActivityUri(ServerRequestInterface $request, Activity $activity)
{
    return (string)$request
        ->getUri()
        ->withPath('/me/family/activities/' . $activity->getId());
}

function authorizeCurrentUserToReadActivity(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $activity = $request->getAttribute(READ_ACTIVITY_KEY);
    
    $currentUser = $request->getAttribute(
        AuthenticationMiddleware::CURRENT_USER_KEY
    );
    
    if (!$currentUser->getFamily()->hasActivity($activity)) {
        return new EmptyResponse(
            403,
            $response->getHeaders()
        );
    }
    
    return $next($request, $response);
}

function authorizeCurrentUserToUpdateActivity(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    return authorizeCurrentUserToReadActivity($request, $response, $next);
}

function activityLinkToJson(
    ServerRequestInterface $request,
    ActivityLink $activityLink
) {
    return [
        'title' => $activityLink->getTitle(),
        'uri' => $activityLink->getURI()
    ];
}

function activityToJson(ServerRequestInterface $request, Activity $activity)
{
    return [
        'name' => $activity->getName(),
        'description' => $activity->getDescription(),
        'activityLinks' => array_map(
            function (ActivityLink $activityLink) use ($request) {
                return activityLinkToJson($request, $activityLink);
            },
            $activity->getLinks()->getValues()
        ),
        'links' => [
            'self' => getActivityUri($request, $activity)
        ]
    ];
}

function partialActivityToJson(
    ServerRequestInterface $request,
    Activity $activity
) {
    return [
        'name' => $activity->getName(),
        'links' => [
            'self' => getActivityUri($request, $activity)
        ]
    ];
}

function activitiesToJson(ServerRequestInterface $request, Family $family)
{
    return array_map(
        function (Activity $activity) use ($request) {
            return partialActivityToJson($request, $activity);
        },
        $family->getActivities()->getValues()
    );
}

function validateActivityJsonForCreate(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $json = $request->getParsedBody();
    
    $validator = v::arrType()->
        keyset(
            v::key('name', v::strType()->length(0, 64)),
            v::key('description', v::strType()->length(0, 4096), false)
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

function validateActivityJsonForUpdate(
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) {
    $activity = $request->getAttribute(READ_ACTIVITY_KEY);
    $json = $request->getParsedBody();
    
    $validator = v::arrType()->
        keyset(
            v::key('name', v::strType()->length(0, 64)),
            v::key('description', v::strType()->length(0, 4096), false),
            v::key(
                'activityLinks',
                v::arrType()->each(
                    v::arrType()->keyset(
                        v::key('title', v::strType()->length(1, 64)),
                        v::key('uri', v::url())
                    )
                ),
                false
            ),
            v::key(
                'links',
                v::arrType()->keySet(
                    v::key(
                        'self',
                        v::equals(getActivityUri($request, $activity))
                    )
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
    }
    
    return $next($request, $response);
}

function createUpdateActivityLinksMiddleware(ObjectManager $db)
{
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) use (
        $db
    ) {
        $activity = $request->getAttribute(READ_ACTIVITY_KEY);
        $json = $request->getParsedBody();
        
        if (array_key_exists('activityLinks', $json)) {
            $activityLinks = array_map(
                function (array $activityJson) {
                    return new ActivityLink(
                        $activityJson['title'],
                        $activityJson['uri']
                    );
                },
                $json['activityLinks']
            );
            
            // First remove all links which no longer apply
            $currentLinks = $activity->getLinks();
            for ($i = $currentLinks->count() - 1; $i >= 0; --$i) {
                $link = $currentLinks[$i];
                $linkFound = false;
                foreach ($activityLinks as $newLink) {
                    if ($newLink->equals($link)) {
                        $linkFound = true;
                        break;
                    }
                }
                
                if (!$linkFound) {
                    $activity->removeLinkAtIndex($i);
                    $db->remove($link);
                }
            }
            
            // Now add the new links
            foreach ($activityLinks as $activityLink) {
                if (!$activity->hasLink($activityLink)) {
                    if ($activity->hasMaxLinks()) {
                        return new JsonResponse(
                            ['Activity has too many links'],
                            400,
                            $response->getHeaders()
                        );
                    }
                    
                    $activity->addLink($activityLink);
                    $db->persist($activityLink);
                }
            }
        }
        
        return $next($request, $response);
    };
}

function updateActivityFromJson(Activity $activity, array $json)
{
    $activity->setName($json['name']);
    
    if (array_key_exists('description', $json)) {
        $activity->setDescription($json['description']);
    } else {
        $activity->setDescription('');
    }
}

class ActivityController
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
        
        $readActivityMiddleware =
            \App\Http\Controllers\createReadObjectArgumentsMiddleware(
                $this->db,
                Activity::class,
                READ_ACTIVITY_KEY
            );
        
        switch ($methodName) {
            case 'getActivities':
                break;
            
            case 'getActivity':
                $middleware[] = $readActivityMiddleware;
                $middleware[] =
                    '\App\Http\Controllers\authorizeCurrentUserToReadActivity';
                break;
            
            case 'createActivity':
                $middleware[] = new ParseAsJsonMiddleware();
                $middleware[] =
                    '\App\Http\Controllers\validateActivityJsonForCreate';
                break;
            
            case 'updateActivity':
                $middleware[] = new ParseAsJsonMiddleware();
                $middleware[] = $readActivityMiddleware;
                $middleware[] =
                    '\App\Http\Controllers\authorizeCurrentUserToUpdateActivity';
                $middleware[] =
                    '\App\Http\Controllers\validateActivityJsonForUpdate';
                $middleware[] = createUpdateActivityLinksMiddleware($this->db);
                break;
        }
        
        return $middleware;
    }
    
    public function getActivities(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        return new JsonResponse(
            activitiesToJson($request, $currentUser->getFamily()),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
    
    public function createActivity(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        $family = $currentUser->getFamily();
        
        $json = $request->getParsedBody();
        $activity = new Activity($json['name']);
        updateActivityFromJson($activity, $json);
        
        if (!$family->hasActivity($activity)) {
            if ($family->hasMaxActivities()) {
                return new JsonResponse(
                    ['Family has too many activities'],
                    400,
                    $response->getHeaders()
                );
            }
            
            $family->addActivity($activity);
            
            $this->db->persist($activity);
            $this->db->flush();
        }
        
        return new JsonResponse(
            activityToJson($request, $activity),
            201,
            $response->getHeaders()
        );
    }
    
    public function getActivity(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        $activity = $request->getAttribute(READ_ACTIVITY_KEY);
        
        return new JsonResponse(
            activityToJson($request, $activity),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
    
    public function updateActivity(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $currentUser = $request->getAttribute(
            AuthenticationMiddleware::CURRENT_USER_KEY
        );
        
        $activity = $request->getAttribute(READ_ACTIVITY_KEY);
        $json = $request->getParsedBody();
        
        updateActivityFromJson($activity, $json);
        
        $this->db->flush();
        
        return new JsonResponse(
            activityToJson($request, $activity),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
}

<?php namespace Test\Http\Controllers;

use Test\Http\Controllers\Utilities\AppRunner;

require_once __DIR__ . '/../../Utilities.php';

class ActivityControllerTest extends \PHPUnit_Framework_TestCase
{
    private $appRunner;
    
    public function setUp()
    {
        $this->appRunner = new AppRunner();
    }
    
    public function testGetActivitiesUnauthenticated()
    {
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testGetActivitiesDoesNotAcceptJson()
    {
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('text/plain');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testGetActivitiesWithoutSavedFamily()
    {
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetActivitiesWithSavedFamily()
    {
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testAddActivityUnauthenticated()
    {
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(
            ['name' => 'test activity'],
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testAddActivityDoesNotAcceptJson()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('text/plain');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(
            ['name' => 'test activity'],
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testAddActivityNonJsonContent()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody('foo', 'text/plain');
        
        $response = $this->appRunner->run();
        $this->assertEquals(415, $response->getStatusCode());
    }
    
    public function testAddActivityMissingName()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody([], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testAddActivityMaxActivities()
    {
        $family = $this->appRunner->createFamily();
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        while (!$family->hasMaxActivities()) {
            $this->appRunner->createActivity('test activity', $family);
        }
        
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(
            ['name' => 'test activity'],
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testAddActivityWithSavedFamily()
    {
        $family = $this->appRunner->createFamily();
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(
            ['name' => 'test activity'],
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(201, $response->getStatusCode());
        
        $expected = [
            'name' => 'test activity',
            'description' => '',
            'activityLinks' => [],
            'links' => [
                'self' => 'http://example.com/me/family/activities/1'
            ]
        ];
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testAddActivityWithoutSavedFamily()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(
            ['name' => 'test activity'],
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(201, $response->getStatusCode());
        
        $expected = [
            'name' => 'test activity',
            'description' => '',
            'activityLinks' => [],
            'links' => [
                'self' => 'http://example.com/me/family/activities/1'
            ]
        ];
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testAddActivityOptionalFields()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToActivities();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $body = [
            'name' => 'test activity',
            'description' => 'description'
        ];
        
        $this->appRunner->setBody($body, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(201, $response->getStatusCode());
        
        $expected = \Test\cloneArray($body);
        $expected['activityLinks'] = [];
        $expected['links'] = [
            'self' => 'http://example.com/me/family/activities/1'
        ];
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetActivityUnauthenticated()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $user = $this->appRunner->createUser('test user', $family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testGetActivityDoesNotAcceptJson()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('text/plain');
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testGetUnknownActivity()
    {
        $family = $this->appRunner->createFamily();
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity(1);
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testGetActivityFromDifferentFamily()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(403, $response->getStatusCode());
    }
    
    public function testGetActivity()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'name' => 'test activity',
            'description' => '',
            'activityLinks' => [],
            'links' => [
                'self' => 'http://example.com/me/family/activities/1',
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateActivityUnauthenticated()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test'
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testUpdateActivityDoesNotAcceptJson()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('text/plain');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test'
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testUpdateActivityNonJsonContent()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $this->appRunner->setBody('foo', 'text/plain');
        
        $response = $this->appRunner->run();
        $this->assertEquals(415, $response->getStatusCode());
    }
    
    public function testUpdateActivityMissingName()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'description' => 'description'
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateActivityInvalidSelfLink()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test',
            'links' => [
                'self' => 'http://example.com/me/family/activities/2'
            ]
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateActivityUnknownActivity()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity(2);
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test'
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testUpdateActivityFromDifferentFamily()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $otherUser = $this->appRunner->createUser('test user', $family);
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test'
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(403, $response->getStatusCode());
    }
    
    public function testUpdateActivity()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test'
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $expected = [
            'name' => 'test',
            'description' => '',
            'activityLinks' => [],
            'links' => [
                'self' => 'http://example.com/me/family/activities/1'
            ]
        ];
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateActivityOptionalFields()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $expected = [
            'name' => 'test',
            'description' => 'description',
            'activityLinks' => [
                [
                    'title' => 'link1',
                    'uri' => 'http://example.com'
                ],
                [
                    'title' => 'link2',
                    'uri' => 'http://example.com/img.jpg'
                ]
            ],
            'links' => [
                'self' => 'http://example.com/me/family/activities/1'
            ]
        ];
        
        $this->appRunner->setBody($expected, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateActivityAddActivityLink()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $this->appRunner->createActivityLink(
            'link1',
            'http://example.com',
            $activity
        );
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $expected = [
            'name' => 'test',
            'description' => 'description',
            'activityLinks' => [
                [
                    'title' => 'link1',
                    'uri' => 'http://example.com'
                ],
                [
                    'title' => 'link2',
                    'uri' => 'http://example.com/img.jpg'
                ]
            ],
            'links' => [
                'self' => 'http://example.com/me/family/activities/1'
            ]
        ];
        
        $this->appRunner->setBody($expected, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateActivityAddTooManyActivityLinks()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        
        $activityLinks = [];
        while (!$activity->hasMaxLinks()) {
            $link = [
                'title' => 'link' . $activity->getLinks()->count(),
                'uri' => 'http://example.com'
            ];
            
            $activityLinks[] = $link;
            
            $this->appRunner->createActivityLink(
                $link['title'],
                $link['uri'],
                $activity
            );
        }
        
        $activityLinks[] = [
            'title' => 'too many',
            'uri' => 'http://example.com'
        ];
        
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $body = [
            'name' => 'test',
            'description' => 'description',
            'activityLinks' => $activityLinks,
            'links' => [
                'self' => 'http://example.com/me/family/activities/1'
            ]
        ];
        
        $this->appRunner->setBody($body, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateActivityRemoveActivityLink()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $this->appRunner->createActivityLink(
            'link1',
            'http://example.com',
            $activity
        );
        $this->appRunner->createActivityLink(
            'link2',
            'http://example.com',
            $activity
        );
        $this->appRunner->createActivityLink(
            'link3',
            'http://example.com',
            $activity
        );
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $expected = [
            'name' => 'test',
            'description' => 'description',
            'activityLinks' => [
                [
                    'title' => 'link1',
                    'uri' => 'http://example.com'
                ],
                [
                    'title' => 'link3',
                    'uri' => 'http://example.com'
                ]
            ],
            'links' => [
                'self' => 'http://example.com/me/family/activities/1'
            ]
        ];
        
        $this->appRunner->setBody($expected, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateActivityAddAndRemoveActivityLinks()
    {
        $family = $this->appRunner->createFamily();
        $activity = $this->appRunner->createActivity('test activity', $family);
        $this->appRunner->createActivityLink(
            'link1',
            'http://example.com',
            $activity
        );
        $this->appRunner->createActivityLink(
            'link2',
            'http://example.com',
            $activity
        );
        $this->appRunner->createActivityLink(
            'link3',
            'http://example.com',
            $activity
        );
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToActivity($activity->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $expected = [
            'name' => 'test',
            'description' => 'description',
            'activityLinks' => [
                [
                    'title' => 'link1',
                    'uri' => 'http://example.com'
                ],
                [
                    'title' => 'link2',
                    'uri' => 'http://example.com/img.jpg'
                ],
                [
                    'title' => 'link4',
                    'uri' => 'http://example.com/img.jpg'
                ]
            ],
            'links' => [
                'self' => 'http://example.com/me/family/activities/1'
            ]
        ];
        
        $this->appRunner->setBody($expected, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    private function setPathToActivities()
    {
        $this->appRunner->setRequestPath('/me/family/activities');
    }
    
    private function setPathToActivity($activityId)
    {
        $this->appRunner->setRequestPath(
            '/me/family/activities/' . $activityId
        );
    }
}

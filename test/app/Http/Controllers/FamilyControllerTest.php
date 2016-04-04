<?php namespace Test\Http\Controllers;

use Test\Http\Controllers\Utilities\AppRunner;

require_once __DIR__ . '/../../Utilities.php';

class FamilyControllerTest extends \PHPUnit_Framework_TestCase
{
    private $appRunner;
    
    public function setUp()
    {
        $this->appRunner = new AppRunner();
    }
    
    public function testGetMyFamilyUnauthenticated()
    {
        $this->setPathToFamily();
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testGetMyFamilyDoesNotAcceptJson()
    {
        $this->setPathToFamily();
        $this->appRunner->setAcceptHeader('text/plain');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testGetMyFamilyWithoutSavedFamily()
    {
        $this->setPathToFamily();
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'members' => [
                [
                    'name' => 'currentUser',
                    'links' => [
                        'self' => 'http://example.com/users/1',
                        'family' => 'http://example.com/me/family',
                        'invitations' => 'http://example.com/me/invitations'
                    ]
                ]
            ],
            'children' => []
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetMyFamilyWithSavedFamily()
    {
        $this->setPathToFamily();
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'members' => [
                [
                    'name' => 'currentUser',
                    'links' => [
                        'self' => 'http://example.com/users/1',
                        'family' => 'http://example.com/me/family',
                        'invitations' => 'http://example.com/me/invitations'
                    ]
                ]
            ],
            'children' => []
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testAddChildUnauthenticated()
    {
        $this->setPathToChildren();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(['name' => 'test'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testAddChildDoesNotAcceptJson()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToChildren();
        $this->appRunner->setAcceptHeader('text/plain');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(['name' => 'test'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testAddChildNonJsonContent()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToChildren();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody('foo', 'text/plain');
        
        $response = $this->appRunner->run();
        $this->assertEquals(415, $response->getStatusCode());
    }
    
    public function testAddChildMissingName()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToChildren();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody([], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testAddChildWithSavedFamily()
    {
        $family = $this->appRunner->createFamily();
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChildren();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(['name' => 'test'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(201, $response->getStatusCode());
        
        $expected = [
            'name' => 'test',
            'skills' => \App\Assets\getDefaultSkills(),
            'links' => [
                'self' => 'http://example.com/me/family/children/1'
            ]
        ];
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testAddChildWithoutSavedFamily()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToChildren();
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('POST');
        
        $this->appRunner->setBody(['name' => 'test'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(201, $response->getStatusCode());
        
        $expected = [
            'name' => 'test',
            'skills' => \App\Assets\getDefaultSkills(),
            'links' => [
                'self' => 'http://example.com/me/family/children/1'
            ]
        ];
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateChildUnauthenticated()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test',
            'skills' => $child->getSkills()
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testUpdateChildDoesNotAcceptJson()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('text/plain');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test',
            'skills' => $child->getSkills()
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testUpdateChildNonJsonContent()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $this->appRunner->setBody('foo', 'text/plain');
        
        $response = $this->appRunner->run();
        $this->assertEquals(415, $response->getStatusCode());
    }
    
    public function testUpdateChildMissingName()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'skills' => $child->getSkills()
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateChildMissingSkills()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test'
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateChildInvalidSkills()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test',
            'skills' => []
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateChildInvalidSelfLink()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test',
            'skills' => $child->getSkills(),
            'links' => [
                'self' => 'http://example.com/me/family/children/2'
            ]
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateChildUnknownChild()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild(2);
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test',
            'skills' => $child->getSkills()
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testUpdateChildFromDifferentFamily()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $otherUser = $this->appRunner->createUser('test user', $family);
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $update = [
            'name' => 'test',
            'skills' => $child->getSkills()
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(403, $response->getStatusCode());
    }
    
    public function testUpdateChild()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $updatedSkills = \Test\cloneArray($child->getSkills());
        $updatedSkills[0][3] = 2;
        
        $update = [
            'name' => 'test',
            'skills' => $updatedSkills
        ];
        
        $this->appRunner->setBody($update, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $expected = [
            'name' => 'test',
            'skills' => $updatedSkills,
            'links' => [
                'self' => 'http://example.com/me/family/children/1'
            ]
        ];
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateChildOptionalFields()
    {
        $family = $this->appRunner->createFamily();
        $child = $this->appRunner->createChild('test child', $family);
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        $this->setPathToChild($child->getId());
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setRequestMethod('PUT');
        
        $expected = [
            'name' => 'test',
            'skills' => $child->getSkills(),
            'links' => [
                'self' => 'http://example.com/me/family/children/1'
            ]
        ];
        
        $this->appRunner->setBody($expected, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    private function setPathToFamily()
    {
        $this->appRunner->setRequestPath('/me/family');
    }
    
    private function setPathToChildren()
    {
        $this->appRunner->setRequestPath('/me/family/children');
    }
    
    private function setPathToChild($childId)
    {
        $this->appRunner->setRequestPath('/me/family/children/' . $childId);
    }
}

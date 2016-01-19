<?php namespace Test\Http\Controllers;

use Test\Http\Controllers\Utilities\AppRunner;

class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    private $appRunner;
    
    public function setUp()
    {
        $this->appRunner = new AppRunner();
    }
    
    public function testGetMeUnauthenticated()
    {
        $this->setPathToMe();
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testGetMeDoesNotAcceptJson()
    {
        $this->setPathToMe();
        $this->appRunner->setAcceptHeader('text/plain');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testGetMeSuccess()
    {
        $this->setPathToMe();
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'name' => 'currentUser',
            'links' => [
                'self' => 'http://example.com/users/1',
                'family' => 'http://example.com/me/family',
                'invitations' => 'http://example.com/me/invitations'
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetUserUnauthenticated()
    {
        $user = $this->appRunner->createUser('user');
        $this->setPathToUser($user->getId());
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testGetUserDoesNotAcceptJson()
    {
        $user = $this->appRunner->createUser('user');
        $this->setPathToUser($user->getId());
        $this->appRunner->setAcceptHeader('text/plain');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testGetUserDoesNotExist()
    {
        $this->setPathToUser(2);
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testGetUserInvalidUserId()
    {
        $this->setPathToUser('foo');
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testGetUserSameUser()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        $this->setPathToUser($currentUser->getId());
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'name' => 'currentUser',
            'links' => [
                'self' => 'http://example.com/users/1',
                'family' => 'http://example.com/me/family',
                'invitations' => 'http://example.com/me/invitations'
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetUserInSameFamily()
    {
        $family = $this->appRunner->createFamily();
        
        $currentUser = $this->appRunner->setAuthenticated($family);
        $user = $this->appRunner->createUser('user', $family);
        
        $this->setPathToUser($user->getId());
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'name' => 'user',
            'links' => [
                'self' => 'http://example.com/users/2'
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetUserInDifferentFamily()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        $user = $this->appRunner->createUser('user');
        
        $this->setPathToUser($user->getId());
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(403, $response->getStatusCode());
    }
    
    public function testUpdateUserUnauthenticated()
    {
        $user = $this->appRunner->createUser('user');
        
        $this->setPathToUser($user->getId());
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setBody(['name' => 'otherUser'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testUpdateUserDoesNotAcceptJson()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToUser($currentUser->getId());
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('text/plain');
        $this->appRunner->setBody(['name' => 'otherUser'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testUpdateUserDoesNotExist()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToUser(2);
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setBody(['name' => 'otherUser'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testUpdateUserSameUser()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToUser($currentUser->getId());
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setBody(['name' => 'otherUser'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'name' => 'otherUser',
            'links' => [
                'self' => 'http://example.com/users/1',
                'family' => 'http://example.com/me/family',
                'invitations' => 'http://example.com/me/invitations'
            ]
        ];
        
        $this->assertEquals($expected, $actual);
        
        $this->appRunner->setRequestMethod('GET');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateUserDifferentUser()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        $user = $this->appRunner->createUser('user');
        
        $this->setPathToUser($user->getId());
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setBody(['name' => 'otherUser'], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(403, $response->getStatusCode());
    }
    
    public function testUpdateUserMissingName()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToUser($currentUser->getId());
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setBody([], 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateUserWithOptionalFields()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToUser($currentUser->getId());
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('application/json');
        
        $expected = [
            'name' => 'otherUser',
            'links' => [
                'self' => 'http://example.com/users/1',
                'family' => 'http://example.com/me/family',
                'invitations' => 'http://example.com/me/invitations'
            ]
        ];
        
        $this->appRunner->setBody($expected, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
        
        $this->appRunner->setRequestMethod('GET');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testUpdateUserInvalidSelfLink()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToUser($currentUser->getId());
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('application/json');
        
        $expected = [
            'name' => 'otherUser',
            'links' => [
                'self' => 'http://example.com/users/2',
                'family' => 'http://example.com/me/family'
            ]
        ];
        
        $this->appRunner->setBody($expected, 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testUpdateUserNonJsonContent()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->setPathToUser($currentUser->getId());
        $this->appRunner->setRequestMethod('PUT');
        $this->appRunner->setAcceptHeader('application/json');
        $this->appRunner->setBody('foo', 'text/plain');
        
        $response = $this->appRunner->run();
        $this->assertEquals(415, $response->getStatusCode());
    }
    
    private function setPathToMe()
    {
        $this->appRunner->setRequestPath('/me');
    }
    
    private function setPathToUser($userId)
    {
        $this->appRunner->setRequestPath('/users/' . $userId);
    }
}

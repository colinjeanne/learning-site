<?php namespace Test\Http\Controllers;

use Test\Http\Controllers\Utilities\AppRunner;

class InvitationsControllerTest extends \PHPUnit_Framework_TestCase
{
    private $appRunner;
    
    public function setUp()
    {
        $this->appRunner = new AppRunner();
    }
    
    public function testGetMyInvitationsUnauthenticated()
    {
        $this->setPathToMyInvitations();
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testGetMyInvitationsDoesNotAcceptJson()
    {
        $this->setPathToMyInvitations();
        $this->appRunner->setAcceptHeader('text/plain');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testGetMyInvitations()
    {
        $this->setPathToMyInvitations();
        $this->appRunner->setAcceptHeader('application/json');
        
        $family = $this->appRunner->createFamily();
        $createdBy = $this->appRunner->createUser('createdBy', $family);
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $invitation = $this->appRunner->createFamilyInvitation(
            $currentUser,
            $createdBy
        );
        
        $otherFamily = $this->appRunner->createFamily();
        $otherCreatedBy = $this->appRunner->createUser(
            'otherCreatedBy',
            $otherFamily
        );
        
        $otherInvitation = $this->appRunner->createFamilyInvitation(
            $currentUser,
            $otherCreatedBy
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            [
                'id' =>
                    'http://example.com/me/invitations/' .
                        $invitation->getId(),
                'createdBy' =>
                    'http://example.com/users/' . $createdBy->getId()
            ],
            [
                'id' =>
                    'http://example.com/me/invitations/' .
                        $otherInvitation->getId(),
                'createdBy' =>
                    'http://example.com/users/' . $otherCreatedBy->getId()
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetMyInvitationsNoInvitations()
    {
        $this->setPathToMyInvitations();
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testInviteFamilyMemberUnauthenticated()
    {
        $this->setPathToMyInvitations();
        $user = $this->appRunner->createUser('user');
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/2',
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testInviteFamilyMemberUserDoesNotExist()
    {
        $this->setPathToMyInvitations();
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/2',
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testInviteFamilyMemberUserAlreadyInFamily()
    {
        $this->setPathToMyInvitations();
        $currentUser = $this->appRunner->setAuthenticated();
        
        $family = $this->appRunner->createFamily();
        $user = $this->appRunner->createUser('user', $family);
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/2',
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testInviteFamilyMemberAddSelf()
    {
        $this->setPathToMyInvitations();
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/1',
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(204, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'members' => [
                'http://example.com/users/1'
            ]
        ];
        
        $this->appRunner->setRequestMethod('GET');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testInviteFamilyMemberAddCurrentMember()
    {
        $this->setPathToMyInvitations();
        
        $family = $this->appRunner->createFamily();
        $currentUser = $this->appRunner->setAuthenticated($family);
        $user = $this->appRunner->createUser('user', $family);
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/2',
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(204, $response->getStatusCode());
        
        $this->appRunner->setRequestMethod('GET');
        $this->appRunner->authenticateAs($user);
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testInviteFamilyMemberNewMember()
    {
        $this->setPathToMyInvitations();
        
        $currentUser = $this->appRunner->setAuthenticated();
        $user = $this->appRunner->createUser('user');
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/2',
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(204, $response->getStatusCode());
        
        $this->appRunner->setRequestMethod('GET');
        $this->appRunner->authenticateAs($user);
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $user->getInvitations()->count());
        
        $invitationId = $user->getInvitations()[0]->getId();
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            [
                'id' => 'http://example.com/me/invitations/' . $invitationId,
                'createdBy' =>
                    'http://example.com/users/' . $currentUser->getId()
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testInviteFamilyMemberInviteAlreadyInvitedMember()
    {
        $this->setPathToMyInvitations();
        
        $family = $this->appRunner->createFamily();
        $currentUser = $this->appRunner->setAuthenticated($family);
        $otherUser = $this->appRunner->createUser('otherUser', $family);
        $user = $this->appRunner->createUser('user');
        
        $invitation = $this->appRunner->createFamilyInvitation(
            $user,
            $otherUser
        );
        
        $invitationId = $invitation->getId();
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/2',
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(204, $response->getStatusCode());
        
        $this->appRunner->setRequestMethod('GET');
        $this->appRunner->authenticateAs($user);
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $user->getInvitations()->count());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            [
                'id' => 'http://example.com/me/invitations/' . $invitationId,
                'createdBy' =>
                    'http://example.com/users/' . $otherUser->getId()
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testInviteFamilyMemberMissingUserId()
    {
        $this->setPathToMyInvitations();
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody('', 'application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testInviteFamilyMemberInvalidUserId()
    {
        $this->setPathToMyInvitations();
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/foo',
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testInviteFamilyMemberNonJsonContent()
    {
        $this->setPathToMyInvitations();
        $currentUser = $this->appRunner->setAuthenticated();
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody('foo', 'text/plain');
        
        $response = $this->appRunner->run();
        $this->assertEquals(415, $response->getStatusCode());
    }
    
    public function testInviteFamilyMemberFamilyIsFull()
    {
        $this->setPathToMyInvitations();
        $family = $this->appRunner->createFamily();
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        while (!$family->isFull()) {
            $this->appRunner->createUser('familyMember', $family);
        }
        
        $user = $this->appRunner->createUser('user');
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/' . $user->getId(),
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testInviteFamilyMemberTooManyFamilyInvitations()
    {
        $this->setPathToMyInvitations();
        $family = $this->appRunner->createFamily();
        $currentUser = $this->appRunner->setAuthenticated($family);
        
        while ($family->canInviteMembers()) {
            $otherUser = $this->appRunner->createUser('otherUser');
            
            $this->appRunner->createFamilyInvitation($otherUser, $currentUser);
        }
        
        $user = $this->appRunner->createUser('user');
        
        $this->appRunner->setRequestMethod('POST');
        $this->appRunner->setBody(
            'http://example.com/users/' . $user->getId(),
            'application/json'
        );
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testAcceptInvitationUnauthenticated()
    {
        $currentUser = $this->appRunner->createUser('currentUser');
        $family = $this->appRunner->createFamily();
        $createdBy = $this->appRunner->createUser('user', $family);
        
        $invitation = $this->appRunner->createFamilyInvitation(
            $currentUser,
            $createdBy
        );
        
        $this->setPathToInvitation($invitation->getId());
        $this->appRunner->setRequestMethod('POST');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testAcceptInvitationInvitationDoesNotExist()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $invitationId = 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa';
        $this->setPathToInvitation($invitationId);
        $this->appRunner->setRequestMethod('POST');
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testAcceptInvitationInvalidInvitationId()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $invitationId = 'foo';
        $this->setPathToInvitation($invitationId);
        $this->appRunner->setRequestMethod('POST');
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testAcceptInvitationOtherUsersInvitation()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $otherUser = $this->appRunner->createUser('otherUser');
        $family = $this->appRunner->createFamily();
        $createdBy = $this->appRunner->createUser('user', $family);
        
        $invitation = $this->appRunner->createFamilyInvitation(
            $otherUser,
            $createdBy
        );
        
        $this->setPathToInvitation($invitation->getId());
        $this->appRunner->setRequestMethod('POST');
        
        $response = $this->appRunner->run();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testAcceptInvitationFamilyIsFull()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $family = $this->appRunner->createFamily();
        $createdBy = $this->appRunner->createUser('createdBy', $family);
        
        while (!$family->isFull()) {
            $this->appRunner->createUser('familyMember', $family);
        }
        
        $invitation = $this->appRunner->createFamilyInvitation(
            $currentUser,
            $createdBy
        );
        
        $this->setPathToInvitation($invitation->getId());
        $this->appRunner->setRequestMethod('POST');
        
        $response = $this->appRunner->run();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testAcceptInvitationAcceptInvitation()
    {
        $currentUser = $this->appRunner->setAuthenticated();
        
        $family = $this->appRunner->createFamily();
        $createdBy = $this->appRunner->createUser('user', $family);
        
        $invitation = $this->appRunner->createFamilyInvitation(
            $currentUser,
            $createdBy
        );
        
        $otherFamily = $this->appRunner->createFamily();
        $otherCreatedBy = $this->appRunner->createUser(
            'otherUser',
            $otherFamily
        );
        
        $otherInvitation = $this->appRunner->createFamilyInvitation(
            $currentUser,
            $otherCreatedBy
        );
        
        $this->setPathToInvitation($invitation->getId());
        $this->appRunner->setRequestMethod('POST');
        
        $response = $this->appRunner->run();
        $this->assertEquals(204, $response->getStatusCode());
        
        $this->setPathToMyInvitations();
        $this->appRunner->setRequestMethod('GET');
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [];
        
        $this->assertEquals($expected, $actual);
        
        $this->appRunner->setRequestPath('/me/family');
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'members' => [
                'http://example.com/users/2',
                'http://example.com/users/1'
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    private function setPathToMyInvitations()
    {
        $this->appRunner->setRequestPath('/me/invitations');
    }
    
    private function setPathToInvitation($invitationId)
    {
        $this->appRunner->setRequestPath('/me/invitations/' . $invitationId);
    }
}

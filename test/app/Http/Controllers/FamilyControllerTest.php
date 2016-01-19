<?php namespace Test\Http\Controllers;

use Test\Http\Controllers\Utilities\AppRunner;

class FamilyControllerTest extends \PHPUnit_Framework_TestCase
{
    private $appRunner;
    
    public function setUp()
    {
        $this->appRunner = new AppRunner();
        $this->appRunner->setRequestPath('/me/family');
    }
    
    public function testGetMyFamilyUnauthenticated()
    {
        $this->appRunner->setAcceptHeader('application/json');
        
        $response = $this->appRunner->run();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    public function testGetMyFamilyDoesNotAcceptJson()
    {
        $this->appRunner->setAcceptHeader('text/plain');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(406, $response->getStatusCode());
    }
    
    public function testGetMyFamilyWithoutSavedFamily()
    {
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'members' => [
                'http://example.com/users/1'
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetMyFamilyWithSavedFamily()
    {
        $this->appRunner->setAcceptHeader('application/json');
        
        $currentUser = $this->appRunner->setAuthenticated();
        
        $response = $this->appRunner->run();
        $this->assertEquals(200, $response->getStatusCode());
        
        $actual = $this->appRunner->getResponseJson($response);
        
        $expected = [
            'members' => [
                'http://example.com/users/1'
            ]
        ];
        
        $this->assertEquals($expected, $actual);
    }
}

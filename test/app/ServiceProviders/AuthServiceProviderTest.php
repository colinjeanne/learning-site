<?php namespace Test\ServiceProviders;

use App\ServiceProviders\AuthServiceProvider;
use League\Container\Container;

class AuthServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $container = new Container();
        
        $container->addServiceProvider(
            AuthServiceProvider::class
        );
        
        $this->assertInstanceOf(
            \App\Auth\JwtAuthorizer::class,
            $container->get(\App\Auth\JwtAuthorizer::class)
        );
    }
}

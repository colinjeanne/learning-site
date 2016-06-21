<?php namespace Test\ServiceProviders;

use App\ServiceProviders\SessionServiceProvider;
use League\Container\Container;

class SessionServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $container = new Container();

        $container->addServiceProvider(
            SessionServiceProvider::class
        );

        $this->assertInstanceOf(
            \App\Http\Session::class,
            $container->get(\App\Http\Session::class)
        );
    }
}

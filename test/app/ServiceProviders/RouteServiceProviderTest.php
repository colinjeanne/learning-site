<?php namespace Test\ServiceProviders;

use App\ServiceProviders\RouteServiceProvider;
use League\Container\Container;

class RouteServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $container = new Container();
        
        $container->addServiceProvider(
            RouteServiceProvider::class
        );
        
        $this->assertInstanceOf(
            \App\Http\Router::class,
            $container->get(\App\Http\Router::class)
        );
    }
}

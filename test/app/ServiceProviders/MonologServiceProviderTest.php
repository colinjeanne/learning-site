<?php namespace Test\ServiceProviders;

use App\ServiceProviders\MonologServiceProvider;
use League\Container\Container;

class MonologServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $container = new Container();
        
        $container->addServiceProvider(
            MonologServiceProvider::class
        );
        
        $this->assertInstanceOf(
            \Psr\Log\LoggerInterface::class,
            $container->get(\Psr\Log\LoggerInterface::class)
        );
    }
}

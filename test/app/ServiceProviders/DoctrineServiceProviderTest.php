<?php namespace Test\ServiceProviders;

use App\ServiceProviders\DoctrineServiceProvider;
use League\Container\Container;

class DoctrineServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $container = new Container();
        
        $container->addServiceProvider(
            DoctrineServiceProvider::class
        );
        
        $this->assertInstanceOf(
            \Doctrine\Common\Persistence\ObjectManager::class,
            $container->get('doctrine')
        );
    }
}

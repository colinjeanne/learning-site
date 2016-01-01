<?php namespace App\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;

class RouteServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        \App\Http\Router::class
    ];
    
    public function register()
    {
        $this->getContainer()->share(
            \App\Http\Router::class,
            function () {
                return new \App\Http\Router($this->getContainer());
            }
        );
    }
}

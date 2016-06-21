<?php namespace App\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;

class SessionServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        \App\Http\Session::class
    ];

    public function register()
    {
        if (getenv('ENVIRONMENT') === 'testing') {
            $this->getContainer()->share(
                \App\Http\Session::class,
                function () {
                    return new \App\Http\ArraySession();
                }
            );
        } else {
            $this->getContainer()->share(
                \App\Http\Session::class,
                function () {
                    return new \App\Http\SuperGlobalSession();
                }
            );
        }
    }
}

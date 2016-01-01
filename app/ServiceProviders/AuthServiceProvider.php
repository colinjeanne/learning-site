<?php namespace App\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;

class AuthServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        \App\Auth\JwtAuthorizer::class
    ];
    
    public function register()
    {
        if (getenv('ENVIRONMENT') === 'testing') {
            $this->getContainer()->add(
                \App\Auth\JwtAuthorizer::class,
                \App\Auth\UnitTestAuthorizer::class
            );
        } else {
            $this->getContainer()->add(
                \App\Auth\JwtAuthorizer::class,
                \App\Auth\GoogleJwtAuthorizer::class
            )
                ->withArgument(\Psr\Log\LoggerInterface::class);
        }
    }
}

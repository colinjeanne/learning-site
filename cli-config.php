<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Dotenv\Dotenv;
use League\Container\Container;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$container = new Container();

$container->addServiceProvider(
    App\ServiceProviders\DoctrineServiceProvider::class
);

$entityManager = $container->get('doctrine');

return ConsoleRunner::createHelperSet($entityManager);

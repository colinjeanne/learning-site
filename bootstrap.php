<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\App;
use Dotenv\Dotenv;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$app = new App();
$response = $app->run(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
$emitter->emit($response);

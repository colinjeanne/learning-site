<?php namespace App\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MonologServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        \Psr\Log\LoggerInterface::class
    ];
    
    public function register()
    {
        $this->getContainer()->share(
            \Psr\Log\LoggerInterface::class,
            function () {
                $today = strftime('%Y-%m-%d');
                $filename = $today . '.log';
                $logger = new Logger('APP');
                $logger->pushHandler(
                    new StreamHandler(
                        __DIR__ . '/../../storage/logs/' . $filename
                    )
                );
                
                return $logger;
            }
        );
    }
}

<?php namespace App\ServiceProviders;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use League\Container\ServiceProvider\AbstractServiceProvider;

class DoctrineServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'doctrine'
    ];
    
    public function register()
    {
        $this->getContainer()->share(
            'doctrine',
            function () {
                $isDevMode = getenv('IS_DEV_MODE');
                $modelsPath = realpath(__DIR__ . '/../Models');
                $config = Setup::createAnnotationMetadataConfiguration(
                    [$modelsPath],
                    $isDevMode,
                    __DIR__ . '/../../storage/database/proxies'
                );

                $driver = getenv('DOCTRINE_DRIVER');
                $connection = ['driver' => $driver];
                
                $environment = getenv('ENVIRONMENT');
                if ($environment === 'testing') {
                    $connection['memory'] = true;
                } elseif ($driver === 'pdo_sqlite') {
                    $databaseDirectory = realpath(
                        __DIR__ . '/../../storage/database'
                    );
                    $databasePath = $databaseDirectory . '/database.sqlite';
                    $connection['path'] = $databasePath;
                } else {
                    $connection['url'] = getenv('DATABASE_URL');
                }

                return EntityManager::create($connection, $config);
            }
        );
    }
}

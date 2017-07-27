<?php
declare(strict_types=1);

namespace Cart\ServiceProviders;

use Cart\Contracts\ServiceProviderContract;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class DatabaseServiceProviders implements ServiceProviderContract
{

    public function register(Container $container)
    {
        $capsule = new Manager;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'database',
            'username'  => 'root',
            'password'  => 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $container->bind(Manager::class, $capsule);
    }
}
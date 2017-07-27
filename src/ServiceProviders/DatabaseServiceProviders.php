<?php
declare(strict_types=1);

namespace Cart\ServiceProviders;

use Cart\Contracts\ServiceProviderContract;
use Cart\Drivers\DatabaseDriver;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class DatabaseServiceProviders implements ServiceProviderContract
{

    public function register(Container $container) : void
    {
        $capsule = new Manager;
        $prefix = 'app.drivers.database';

        $capsule->addConnection([
            'driver'    => config($prefix . '.driver'),
            'host'      => config($prefix . '.host'),
            'database'  => config($prefix . '.database'),
            'username'  => config($prefix . '.username'),
            'password'  => config($prefix . '.password'),
            'charset'   => config($prefix . '.charset'),
            'collation' => config($prefix . '.collation'),
            'prefix'    => config($prefix . '.prefix'),
        ]);

        $capsule->setEventDispatcher(new Dispatcher(app()));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $container->bind(Manager::class, $capsule);

        $container->bind(DatabaseDriver::class, function () use ($capsule) {
            return new DatabaseDriver($capsule);
        });
    }
}
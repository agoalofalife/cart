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

        $capsule->addConnection([
            'driver'    => config('drivers.database.driver'),
            'host'      => config('drivers.database.host'),
            'database'  => config('drivers.database.database'),
            'username'  => config('drivers.database.username'),
            'password'  => config('drivers.database.password'),
            'charset'   => config('drivers.database.charset'),
            'collation' => config('drivers.database.collation'),
            'prefix'    => config('drivers.database.prefix'),
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
<?php
declare(strict_types=1);

namespace Cart\ServiceProviders;

use Cart\Contracts\ServiceProviderContract;
use Cart\Drivers\RedisDriver;
use Illuminate\Container\Container;
use Predis\Client;

class RedisServiceProvider implements ServiceProviderContract
{
    public function register(Container $container) : void
    {
        $redis =  new Client([
            'scheme' => config('app.drivers.redis.scheme'),
            'host'   => config('app.drivers.redis.host'),
            'port'   => config('app.drivers.redis.port')
        ]);

        $container->instance(Client::class, $redis);
        $container->bind(RedisDriver::class, function () use ($redis) {
            return new RedisDriver($redis);
        });
    }
}
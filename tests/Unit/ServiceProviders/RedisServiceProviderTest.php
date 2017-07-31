<?php
declare(strict_types=1);

namespace Cart\Tests\Unit\ServiceProvider;

use Cart\Drivers\RedisDriver;
use Cart\ServiceProviders\RedisServiceProvider;
use Cart\Tests\TestCase;
use Illuminate\Config\Repository;

class RedisServiceProviderTest extends TestCase
{
    public function testRegister() : void
    {
        $provider = new RedisServiceProvider();
        $this->assertFalse(app()->bound(RedisDriver::class));
        $config = $this->mock(Repository::class);

        $config->shouldReceive('get')->with('cart.drivers.redis.port', null)->andReturn(6379);
        $config->shouldReceive('get')->with('cart.drivers.redis.scheme', null)->andReturn('tcp');
        $config->shouldReceive('get')->with('cart.drivers.redis.prefix', null)->andReturn('cart');
        $config->shouldReceive('get')->with('cart.drivers.redis.host', null)->andReturn('127.0.0.1');

        app()->instance('config', $config);
        $provider->register(app());
        $this->assertTrue(app()->bound(RedisDriver::class));
        $this->assertInstanceOf(RedisDriver::class, app()->make(RedisDriver::class));
    }
}
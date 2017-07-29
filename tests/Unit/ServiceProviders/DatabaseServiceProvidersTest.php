<?php
declare(strict_types=1);
namespace Cart\Tests\Unit\ServiceProvider;

use Cart\Drivers\DatabaseDriver;
use Cart\ServiceProviders\DatabaseServiceProviders;
use Cart\Tests\TestCase;
use Illuminate\Config\Repository;
use Illuminate\Database\Capsule\Manager;

class DatabaseServiceProvidersTest extends TestCase
{

    public function testRegister() : void
    {
        $provider = new DatabaseServiceProviders();

        $this->assertFalse(app()->bound(Manager::class));
        $this->assertFalse(app()->bound(DatabaseDriver::class));
        $config = $this->mock(Repository::class);

        app()->instance('config', $config);
        $config->shouldReceive('get');
        $this->assertFalse(app()->resolved(DatabaseDriver::class));
        $provider->register(app());
        $this->assertTrue(app()->bound(Manager::class));
        $this->assertTrue(app()->bound(DatabaseDriver::class));
    }
}
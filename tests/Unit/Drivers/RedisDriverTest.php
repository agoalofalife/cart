<?php
declare(strict_types=1);
namespace Cart\Tests\Unit\Drivers;

use Cart\Drivers\RedisDriver;
use Cart\Tests\TestCase;
use Illuminate\Config\Repository;
use Illuminate\Support\Manager;
use Mockery\Mock;
use Predis\Client;

class RedisDriverTest extends TestCase
{
    /**
     * @var RedisDriver
     */
    protected $driver;
    /**
     * @var Mock
     */
    protected $redis;

    /**
     * @var Mock
     */
    protected $repository;

    public function setUp()
    {
        $this->redis = $this->mock(Client::class);
        $this->repository = $this->mock(Repository::class);
        $this->driver = new RedisDriver($this->redis);
    }

    public function testAddIsNotValid() : void
    {
        $this->repository->shouldReceive();
        $this->assertFalse($this->driver->add([]));
    }
}
<?php
declare(strict_types=1);
namespace Cart\Tests\Unit\Drivers;

use Cart\Contracts\CounterItemContract;
use Cart\Contracts\DiscountContract;
use Cart\CountOperation\AdditionCount;
use Cart\DiscountStrategy\FixDiscountStrategy;
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

    protected $fakeSerialize = 'a:4:{s:2:"id";i:2;s:7:"user_id";i:2;s:5:"price";i:200;s:5:"count";i:2;}';

    public function setUp()
    {
        $this->redis = $this->mock(Client::class);
        $this->repository = $this->mock(Repository::class);
        app()->instance('config', $this->repository);
        $this->repository->shouldReceive('get')->with('cart.drivers.redis.prefix', null);
        $this->driver = new RedisDriver($this->redis);
    }

    public function testAddIsNotValid() : void
    {
        $this->assertFalse($this->driver->add([]));
    }

    public function testAddIsNotExistUser() : void
    {
        $id = $userId = $this->faker()->uuid;
        $this->redis->shouldReceive('hgetall')->once()->andReturn([]);
        $this->redis->shouldReceive('hset')->once()->andReturn([]);
        $this->driver->add(['id' => $id, 'user_id' => $userId]);
    }

    public function testAddExistUserIncrementItem() : void
    {
        $id = $userId = $this->faker()->uuid;
        $input = ['id' => $id, 'user_id' => $userId];
        $this->redis->shouldReceive('hgetall')->once()->andReturn($input);
        $this->redis->shouldReceive('hset')->once();
        $this->redis->shouldReceive('hget')
            ->times(2)->andReturn($this->fakeSerialize);
        $this->driver->add($input);
    }

    public function testAddItem() : void
    {
        $id = $userId = $this->faker()->uuid;
        $input = ['id' => $id, 'user_id' => $userId];
        $this->redis->shouldReceive('hgetall')->once()->andReturn($input);
        $this->redis->shouldReceive('hget')->andReturn(null);
        $this->redis->shouldReceive('hset')->once();
        $this->driver->add($input);
    }

    public function testRemoveNotValid() : void
    {
        $this->assertFalse($this->driver->remove([]));
    }

    public function testRemove() : void
    {
        $id = $userId = $this->faker()->uuid;
        $input = ['id' => $id, 'user_id' => $userId];
        $this->redis->shouldReceive('hdel')->once();
        $this->assertTrue($this->driver->remove($input));
    }

    public function testClear() : void
    {
        $id = $this->faker()->randomNumber();
        $this->redis->shouldReceive('del')->once()->with($id);
        $this->driver->clear($id);
    }

    public function testChangeIsNotValid() : void
    {
        $this->assertFalse($this->driver->change([], new AdditionCount()));
    }

    public function testChangeIsNotExist() : void
    {
        $id = $userId = $this->faker()->uuid;
        $input = ['id' => $id, 'user_id' => $userId, 'count' => $this->faker()->randomNumber()];
        $counter = $this->mock(CounterItemContract::class);

        $this->redis->shouldReceive('hget')->andReturn(null);
        $this->assertFalse($this->driver->change($input, $counter));
    }

    public function testChangeIsExist() : void
    {
        $id = $userId = $this->faker()->uuid;
        $input = ['id' => $id, 'user_id' => $userId, 'count' => $this->faker()->randomNumber()];
        $counter = $this->mock(CounterItemContract::class);

        $this->redis->shouldReceive('hget')->andReturn($this->fakeSerialize);
        $this->redis->shouldReceive('hdel')->andReturn($this->fakeSerialize)->once();
        $counter->shouldReceive('execute')->once();
        $this->assertTrue($this->driver->change($input, $counter));
    }

    public function testCDiscountIsNotValid() : void
    {
        $this->assertFalse($this->driver->discount(new FixDiscountStrategy($this->faker()->randomNumber()), []));
    }

    public function testCDiscountReturnNull() : void
    {
        $id = $userId = $this->faker()->uuid;
        $input = ['id' => $id, 'user_id' => $userId, 'price' => $this->faker()->randomNumber()];
        $discount = $this->mock(DiscountContract::class);
        $this->redis->shouldReceive('hget')->andReturn(null);
        $this->driver->discount($discount, $input);
    }

    public function testDiscount() : void
    {
        $id = $userId = $this->faker()->uuid;
        $input = ['id' => $id, 'user_id' => $userId, 'price' => $this->faker()->randomNumber()];
        $discount = $this->mock(DiscountContract::class);
        $discount->shouldReceive('make')->once();
        $this->redis->shouldReceive('hget')->andReturn($this->fakeSerialize)->once();
        $this->redis->shouldReceive('hset')->andReturn()->once();
        $this->driver->discount($discount, $input);
    }
}
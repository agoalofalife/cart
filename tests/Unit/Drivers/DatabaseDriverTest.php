<?php
declare(strict_types=1);
namespace Cart\Tests\Unit\Drivers;

use Cart\Contracts\DiscountContract;
use Cart\CountOperation\AdditionCount;
use Cart\CountOperation\ChangeCount;
use Cart\Drivers\DatabaseDriver;
use Cart\Tests\TestCase;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Mockery\Mock;

class DatabaseDriverTest extends TestCase
{
    /**
     * @var DatabaseDriver
     */
    protected $driver;
    /**
     * @var Mock
     */
    protected $capsule;

    public function setUp()
    {
        $this->capsule = $this->mock(Manager::class);
        $this->driver = new DatabaseDriver($this->capsule);
    }

    public function testAddNotValidate() : void
    {
        $this->assertFalse($this->driver->add([]));
    }

    public function testAddIsNotExistUser()  : void
    {
        $builder = $this->mock(Builder::class);
        $this->capsule->shouldReceive('table')->andReturn($builder);
        $builder->shouldReceive('where')->andReturn($builder);
        $builder->shouldReceive('get')->andReturn($builder);
        $builder->shouldReceive('count')->andReturn(0);
        $builder->shouldReceive('insert');
        $this->driver->add(['id' => $this->faker()->randomDigit, 'user_id' => $this->faker()->randomDigit]);
    }

    public function testAddExistItem() : void
    {
        $builder = $this->mock(Builder::class);
        $collection= $this->mock(Collection::class);
        $count = $this->mock(AdditionCount::class);

        app()->instance(AdditionCount::class, $count);

        $this->capsule->shouldReceive('table')->andReturn($builder)->times(4);
        $builder->shouldReceive('where')->andReturn($builder)->times(4);
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->times(3);
        $collection->shouldReceive('count')->andReturn(1);
        $builder->shouldReceive('update')->andReturn($builder)->times(1);
        $collection->shouldReceive('isNotEmpty')->andReturn(true);
        $collection->shouldReceive('make')->andReturn($collection);
        $collection->shouldReceive('contains')->andReturn(true);
        $collection->shouldReceive('isEmpty')->andReturn(false);
        $collection->shouldReceive('first')->andReturn(new class{
            public $data = '[{"id" : "2"}]';
        });

        $count->shouldReceive('execute');
        $this->driver->add(['id' => $this->faker()->randomDigit, 'user_id' => $this->faker()->randomDigit, 'count' => 2]);
    }

    public function testAddIsNotExistItem() : void
    {
        $builder = $this->mock(Builder::class);
        $collection= $this->mock(Collection::class);
        $count = $this->mock(AdditionCount::class);

        app()->instance(AdditionCount::class, $count);

        $this->capsule->shouldReceive('table')->andReturn($builder)->times(4);
        $builder->shouldReceive('where')->andReturn($builder)->times(4);
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->times(3);
        $collection->shouldReceive('count')->andReturn(1);
        $builder->shouldReceive('update')->andReturn($builder)->times(1);
        $collection->shouldReceive('isNotEmpty')->andReturn(true);
        $collection->shouldReceive('make')->andReturn($collection);
        $collection->shouldReceive('contains')->andReturn(false);
        $collection->shouldReceive('first')->andReturn(new class{
            public $data = '[{"id" : "2"}]';
        });

        $this->driver->add(['id' => $this->faker()->randomDigit, 'user_id' => $this->faker()->randomDigit]);
    }

    public function testRemoveNotValid() : void
    {
        $this->assertFalse($this->driver->remove([]));
    }

    public function testRemoveUpdate() : void
    {
        $builder = $this->mock(Builder::class);
        $collection = $this->mock(Collection::class);

        $this->capsule->shouldReceive('table')->andReturn($builder)->times(2);
        $builder->shouldReceive('where')->andReturn($builder)->times(2);
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->times(1);
        $builder->shouldReceive('update')->andReturn($builder)->once();
        $collection->shouldReceive('isNotEmpty')->andReturn(true);

        $collection->shouldReceive('first')->andReturn(new class{
            public $data = '[{"id" : "2", "count" :"0"}]';
        });

        $this->driver->remove(['id' => 1, 'user_id' => $this->faker()->randomDigit]);
    }

    public function testRemoveDelete() : void
    {

        $builder = $this->mock(Builder::class);
        $collection= $this->mock(Collection::class);

        $this->capsule->shouldReceive('table')->andReturn($builder)->times(2);
        $builder->shouldReceive('where')->andReturn($builder)->times(2);
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->times(1);
        $collection->shouldReceive('isNotEmpty')->andReturn(true);
        $builder->shouldReceive('delete')->once();
        $collection->shouldReceive('first')->andReturn(new class{
            public $data = '[{"id" : "2", "count" :"0"}]';
        });

        $this->driver->remove(['id' => 2, 'user_id' => 0]);
    }

    public function testClear() : void
    {
        $builder = $this->mock(Builder::class);

        $this->capsule->shouldReceive('table')->andReturn($builder)->once();
        $builder->shouldReceive('where')->andReturn($builder)->once();
        $builder->shouldReceive('delete')->once();

        $this->driver->clear($this->faker()->randomDigit);
    }

    public function testChangeNotValid() : void
    {
        $changeCount = $this->mock(ChangeCount::class);
        $this->assertFalse($this->driver->change([], $changeCount));
    }

    public function testChangeEmpty() : void
    {
        $builder = $this->mock(Builder::class);
        $collection = $this->mock(Collection::class);
        $changeCount = $this->mock(ChangeCount::class);

        $this->capsule->shouldReceive('table')->andReturn($builder)->once();
        $builder->shouldReceive('where')->andReturn($builder)->once();
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->once();
        $collection->shouldReceive('isEmpty')->andReturn(true);

        $this->driver->change([
            'id' => $this->faker()->randomDigit,
            'user_id' => $this->faker()->randomDigit,
            'count' => $this->faker()->randomDigit], $changeCount);
    }

    public function testChangeRemove() : void
    {
        $builder = $this->mock(Builder::class);
        $collection = $this->mock(Collection::class);
        $changeCount = $this->mock(ChangeCount::class);

        $this->capsule->shouldReceive('table')->andReturn($builder)->times(2);
        $builder->shouldReceive('where')->andReturn($builder)->times(2);
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->times(2);
        $collection->shouldReceive('isEmpty')->andReturn(false);
        $collection->shouldReceive('first')->andReturn(new class{
            public $data = '[{"id": "2", "count" :"0"}]';
        });
        $collection->shouldReceive('isNotEmpty')->andReturn(false);
        $changeCount->shouldReceive('execute')->once()->andReturn(0);
        $this->driver->change([
            'id' => 2,
            'user_id' => $this->faker()->randomDigit,
            'count' => $this->faker()->randomDigit], $changeCount);
    }

    public function testChangeUpdate() : void
    {
        $builder = $this->mock(Builder::class);
        $collection = $this->mock(Collection::class);
        $changeCount = $this->mock(ChangeCount::class);

        $this->capsule->shouldReceive('table')->andReturn($builder)->times(2);
        $builder->shouldReceive('where')->andReturn($builder)->times(2);
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->times(1);
        $collection->shouldReceive('isEmpty')->andReturn(false);
        $collection->shouldReceive('first')->andReturn(new class{
            public $data = '[{"id": "2", "count" :"0"}]';
        });
        $collection->shouldReceive('isNotEmpty')->andReturn(false);
        $changeCount->shouldReceive('execute')->once()->andReturn(1);

        $builder->shouldReceive('update')->once();
        $this->driver->change([
            'id' => 2,
            'user_id' => $this->faker()->randomDigit,
            'count' => $this->faker()->randomDigit], $changeCount);
    }

    public function testSetTable() : void
    {
        $this->driver->setTable($this->faker()->word);
    }

    public function testDiscountNotValid() : void
    {
        $discount = $this->mock(DiscountContract::class);
        $this->assertFalse($this->driver->discount($discount, []));
    }

    public function testDiscountIsNotExistUser() : void
    {
        $id = $this->faker()->randomDigit;
        $userId = $this->faker()->randomDigit;
        $price = $this->faker()->randomDigit;
        $discount = $this->mock(DiscountContract::class);
        $builder = $this->mock(Builder::class);
        $collection = $this->mock(Collection::class);

        $discount->shouldReceive('make')->with($price);
        $this->capsule->shouldReceive('table')->andReturn($builder)->once();
        $builder->shouldReceive('where')->andReturn($builder)->once();
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->once();
        $collection->shouldReceive('isNotEmpty')->andReturn(false);

        $this->driver->discount($discount, ['id' => $id, 'user_id' => $userId, 'price' => $price]);
    }

    public function testDiscount() : void
    {
        $id = $this->faker()->randomDigit;
        $userId = $this->faker()->randomDigit;
        $price = $this->faker()->randomDigit;
        $discount = $this->mock(DiscountContract::class);
        $builder = $this->mock(Builder::class);
        $collection = $this->mock(Collection::class);

        $discount->shouldReceive('make')->with($price);
        $this->capsule->shouldReceive('table')->andReturn($builder)->times(2);
        $builder->shouldReceive('where')->andReturn($builder)->times(2);
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->once();
        $collection->shouldReceive('isNotEmpty')->andReturn(true);
        $builder->shouldReceive('update')->once();

        $collection->shouldReceive('first')->andReturn(new class{
            public $data = '[{"id": "2", "count" :"0"}]';
        })->once();

        $this->driver->discount($discount, ['id' => $id, 'user_id' => $userId, 'price' => $price]);
    }

    public function testDiscountEquals() : void
    {
        $userId   = $this->faker()->randomDigit;
        $price    = $this->faker()->randomDigit;
        $discount = $this->mock(DiscountContract::class);
        $builder  = $this->mock(Builder::class);
        $collection = $this->mock(Collection::class);

        $discount->shouldReceive('make')->with($price);
        $this->capsule->shouldReceive('table')->andReturn($builder)->times(2);
        $builder->shouldReceive('where')->andReturn($builder)->times(2);
        $builder->shouldReceive('get')->andReturn($builder)->andReturn($collection)->once();
        $collection->shouldReceive('isNotEmpty')->andReturn(true);
        $builder->shouldReceive('update')->once();

        $collection->shouldReceive('first')->andReturn(new class{
            public $data = '[{"id": "2", "count" :"0"}]';
        })->once();

        $this->driver->discount($discount, ['id' => 2, 'user_id' => $userId, 'price' => $price]);
    }
}
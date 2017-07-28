<?php
declare(strict_types=1);

namespace Cart\Drivers;

use Cart\Contracts\CartDriverContract;
use Cart\Contracts\CounterItemContract;
use Cart\Contracts\SetTableDriver;
use Cart\CountOperation\AdditionCount;
use Cart\CountOperation\ChangeCount;
use Illuminate\Database\Capsule\Manager;

class DatabaseDriver implements CartDriverContract, SetTableDriver
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $table;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->setTable();
    }

    /**
     * Add new item in cart
     * @param array $item
     * @return bool
     */
    public function add(array $item) : bool
    {
        // validation structure
        if ($this->validate($item, ['id', 'user_id', 'count']) === false) {
            return false;
        }

        $userId = $item['user_id'];
        unset($item['user_id']);

        if ($this->existUser((int)$userId)) {
            if ($this->existItem((int)$item['id'], (int)$userId)) {
                call_user_func([$this, 'incrementItem'], (int)$item['id'], (int)$userId);
            } else {
                call_user_func([$this, 'addItem'], (array)$item, (int)$userId);
            }
        } else {
            $this->addRow((int)$userId, inJson([$item]));
        }

        return true;
    }

    /**
     * Remove item from cart
     * @param array $item
     * @return bool
     */
    public function remove(array $item) : bool
    {
        if ($this->validate($item, ['id', 'user_id']) === false) {
            return false;
        }

        $collection = $this->manager->table($this->table)->where('user_id', '=', $item['user_id'])->get();

        if ($collection->isNotEmpty()) {
            $itemFilter = array_filter(fromJson($collection->first()->data, true), function ($value) use ($item) {
                return $value['id'] != $item['id'];
            });

            if (count($itemFilter) > 0) {
                $this->updateRow($item['user_id'], inJson([$itemFilter]));
            } else {
                $this->deleteRow($item['user_id']);
            }

            return true;
        }
           return false;
    }

    /**
     * Clear cart for concrete relation entity
     * @param int $entityId
     * @return void
     */
    public function clear(int $entityId) : void
    {
        $this->deleteRow($entityId);
    }

    /**
     * Change item(position)
     * @param array $item
     * @return bool
     */
    public function change(array $item) : bool
    {
        if ($this->validate($item, ['id', 'user_id', 'count']) === false) {
            return false;
        }

        app()->bind(ChangeCount::class, ChangeCount::class);

        return $this->counterItem($item['id'], $item['user_id'], $item['count'], app()->make(ChangeCount::class));
    }

    /**
     * Set custom name table
     * @param string $name
     */
    public function setTable(string $name = 'cart_items'): void
    {
        $this->table = $name;
    }

    /**
     * Validate input parameters
     *
     * @param array $item
     * @param array $require
     * @return bool
     * @internal param array $required
     */
    private function validate(array $item, array $require = []) : bool
    {
        foreach ($require as $parameter) {
            if (isset($item[$parameter]) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check exist User in table cart_items
     * @param int $user
     * @return bool
     */
    private function existUser(int $user) : bool
    {
        return $this->manager->table($this->table)->where('user_id', $user)->get()->count() > 0;
    }

    /**
     * Check exit item in json collection
     * @param int      $itemId
     * @param int      $user
     * @return bool
     */
    private function existItem(int $itemId, int $user) : bool
    {
        $collection = $this->manager->table($this->table)->where('user_id', $user)->get();

        if ($collection->isNotEmpty()) {
            return collect(fromJson($collection->first()->data))->contains('id', $itemId);
        }
        return false;
    }

    /**
     * Add new Item in collection
     * @param array $item
     * @param int $user
     */
    private function addItem(array $item, int $user) :void
    {
        $collection = $this->manager->table($this->table)->where('user_id', $user)->get()->first();
        $items = fromJson($collection->data);

        $items[] = $item;

        $this->updateRow((int)$user, inJson($items));
    }

    /**
     * Increment Item in json collection
     * @param int $itemId
     * @param int $user
     */
    private function incrementItem(int $itemId, int $user) :void
    {
        app()->bind(AdditionCount::class, AdditionCount::class);
        $this->counterItem($itemId, $user, 1, app()->make(AdditionCount::class));
    }

    /**
     * @param int                 $itemId
     * @param int                 $user
     * @param int                 $counterUp
     * @param CounterItemContract $typeOperation
     * @return bool
     */
    private function counterItem(int $itemId, int $user, int $counterUp, CounterItemContract $typeOperation) : bool
    {
        $collection = $this->manager->table($this->table)->where('user_id', $user)->get();

        if ($collection->isEmpty()) {
            return false;
        }
        $items = fromJson($collection->first()->data, true);

        $targetItem = array_filter($items, function ($value) use ($itemId) {
            return $value['id'] == $itemId;
        });

        $item = reset($targetItem);

        $item['count'] = $typeOperation->execute((int)$item['count'], $counterUp);

        if ($item['count'] == 0) {
            $this->remove(['id' => $itemId, 'user_id' => $user]);
        } else {
            $this->updateRow((int)$user, inJson([$item]));
        }

        return true;
    }
    /**
     * Empty fill row table
     * @param int   $user
     * @param string $values
     */
    private function addRow(int $user, string $values) : void
    {
        $this->manager->table($this->table)->insert([
            'user_id' => $user,
            'data' => $values
        ]);
    }

    /**
     * Update Row
     * @param int    $user
     * @param string $values
     */
    private function updateRow(int $user, string $values) : void
    {
        $this->manager->table($this->table)->where('user_id', $user)->update([
            'data' => $values
        ]);
    }

    /**
     * @param int $user
     */
    private function deleteRow(int $user) : void
    {
        $this->manager->table($this->table)->where('user_id', $user)->delete();
    }
}
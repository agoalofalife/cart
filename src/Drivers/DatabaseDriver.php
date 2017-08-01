<?php
declare(strict_types=1);

namespace Cart\Drivers;

use Cart\Contracts\CartDriverContract;
use Cart\Contracts\CounterItemContract;
use Cart\Contracts\DiscountContract;
use Cart\Contracts\DiscountDriverContract;
use Cart\Contracts\SetTableDriver;
use Cart\CountOperation\AdditionCount;
use Cart\Traits\Validate;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Collection;

/**
 * Class DatabaseDriver
 *
 * @package Cart\Drivers
 */
class DatabaseDriver implements CartDriverContract, SetTableDriver, DiscountDriverContract
{
    use Validate;
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
        if ($this->validate($item, ['id', 'user_id']) === false) {
            return false;
        }

        if ($this->existUser((int)$item['user_id'])) {
            if ($this->existItem($item)) {
                call_user_func([$this, 'incrementItem'], $item);
            } else {
                call_user_func([$this, 'addItem'], $item);
            }
        } else {
            $item['count'] = 1;
            $this->addRow((int)$item['user_id'], inJson([$item]));
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

        $collection = $this->getItems($item['user_id']);

        if ($collection->isNotEmpty()) {
            $itemFilter = array_filter(fromJson($collection->first()->data, true), function ($value) use ($item) {
                return $value['id'] != $item['id'];
            });

            if (count($itemFilter) > 0) {
                $this->updateRow($item['user_id'], inJson(array_values($itemFilter)));
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
     * @param CounterItemContract $itemContract
     * @return bool
     */
    public function change(array $item, CounterItemContract $itemContract) : bool
    {
        if ($this->validate($item, ['id', 'user_id', 'count']) === false) {
            return false;
        }

        return $this->counterItem($item['id'], $item['user_id'], $item['count'], $itemContract);
    }

    /**
     * Get all items to user id
     * @param int $userId
     * @return array
     */
    public function get(int $userId): array
    {
        $row = $this->getItems($userId);
        return fromJson($row->toJson(), true);
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
     * @param DiscountContract $strategy
     * @param array $items
     * @return bool
     */
    public function discount(DiscountContract $strategy, array $items) : bool
    {
        // validation structure
        if ($this->validate($items, ['id', 'user_id', 'price']) === false) {
            return false;
        }

        $newPrice = $strategy->make($items['price']);
        $row = $this->getItems($items['user_id']);

        if ($row->isNotEmpty()) {
            $transformItems = array_map(function ($value) use ($items, $newPrice) {
                if ($value['id'] == $items['id']) {
                     $value['discount'] = $newPrice;
                     return $value;
                }
                return $value;
            }, fromJson($row->first()->data, true));

            $this->updateRow($items['user_id'], inJson($transformItems));
            return true;
        }
         return false;
    }

    /**
     * Check exist User in table cart_items
     * @param int $userId
     * @return bool
     */
    private function existUser(int $userId) : bool
    {
        return $this->getItems($userId)->count() > 0;
    }

    /**
     * Check exit item in json collection
     *
     * @param array $item
     * @return bool
     * @internal param int $itemId
     * @internal param int $user
     */
    private function existItem(array $item) : bool
    {
        $collection = $this->getItems($item['user_id']);
        return $collection->make(fromJson($collection->first()->data))->contains('id', $item['id']);
    }

    /**
     * Add new Item in collection
     *
     * @param array $item
     * @internal param int $user
     */
    private function addItem(array $item) :void
    {
        $collection = $this->getItems($item['user_id'])->first();
        $items      = fromJson($collection->data);

        $item['count'] = 1;
        $items[] = $item;

        $this->updateRow((int)$item['user_id'], inJson($items));
    }

    /**
     * Increment Item in json collection
     *
     * @param array $item
     * @internal param int $itemId
     * @internal param int $user
     */
    private function incrementItem(array $item) :void
    {
        app()->bind(AdditionCount::class, AdditionCount::class);
        $this->counterItem($item['id'], $item['user_id'], 1, app()->make(AdditionCount::class));
    }

    /**
     * @param int                 $itemId
     * @param int                 $userId
     * @param int                 $counterUp
     * @param CounterItemContract $typeOperation
     * @return bool
     */
    private function counterItem(int $itemId, int $userId, int $counterUp, CounterItemContract $typeOperation) : bool
    {
        $collection = $this->getItems($userId);
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
            $this->remove(['id' => $itemId, 'user_id' => $userId]);
        } else {
            $this->updateRow((int)$userId, inJson([$item]));
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
     * @param int    $userId
     * @param string $values
     */
    private function updateRow(int $userId, string $values) : void
    {
        $this->getItems($userId)->update([
            'data' => $values
        ]);
    }

    /**
     * Get collection items from database
     * @param int $userId
     * @return Collection
     */
    private function getItems(int $userId)
    {
        return $this->manager->table($this->table)->where('user_id', $userId)->get();
    }

    /**
     * @param int $user
     */
    private function deleteRow(int $user) : void
    {
        $this->manager->table($this->table)->where('user_id', $user)->delete();
    }
}
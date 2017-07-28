<?php
declare(strict_types=1);

namespace Cart\Drivers;

use Cart\Contracts\CartDriverContract;
use Cart\Contracts\SetTableDriver;
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
        if (isset($item['id']) === false || isset($item['user_id']) === false || isset($item['count']) === false) {
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

    public function remove(int $id) : bool
    {
        $this->manager->table($this->table)->where('id', '=', $id)->delete();
    }

    public function clear() : void
    {
        $this->manager->table($this->table)->truncate();
    }

    public function change(array $item)
    {
        $this->manager->table($this->table)
            ->where('id', 1)
            ->update($item);
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
        $collection = $this->manager->table($this->table)->where('user_id', $user)->get()->first();

        $items = fromJson($collection->data, true);

        $targetItem = array_filter($items, function ($value) use ($itemId) {
            return $value['id'] == $itemId;
        });

        $item = reset($targetItem);
        $item['count']++;

        $this->updateRow((int)$user, inJson($item));
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
}
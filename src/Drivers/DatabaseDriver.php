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

    public function add(array $item)
    {
        $this->manager->table($this->table)->insert([
            $item
        ]);
    }

    public function remove(int $id)
    {
        $this->manager->table($this->table)->where('id', '=', $id)->delete();
    }

    public function clear()
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
     * Set name table
     * @param string $name
     */
    public function setTable(string $name = 'cart_items'): void
    {
        $this->table = $name;
    }
}
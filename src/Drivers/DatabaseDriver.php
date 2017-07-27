<?php
declare(strict_types=1);

namespace Cart\Drivers;

use Cart\Contracts\CartDriverContract;
use Illuminate\Database\Capsule\Manager;

class DatabaseDriver implements CartDriverContract
{
    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function add(array $item)
    {
        $this->manager->table('users')->insert([
            $item
        ]);
    }

    public function remove(int $id)
    {
        $this->manager->table('users')->where('votes', '>', $id)->delete();
    }

    public function clear()
    {
        $this->manager->table('users')->truncate();
    }

    public function change(array $item)
    {
        $this->manager->table('users')
            ->where('id', 1)
            ->update(['votes' => 1]);
    }
}
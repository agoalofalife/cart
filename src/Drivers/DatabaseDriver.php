<?php
declare(strict_types=1);

namespace Cart\Drivers;

use Cart\Contracts\CartDriverContract;

class DatabaseDriver implements CartDriverContract
{

    public function add(array $item)
    {
        // TODO: Implement add() method.
    }

    public function remove(int $id)
    {
        // TODO: Implement remove() method.
    }

    public function clear()
    {
        // TODO: Implement clear() method.
    }

    public function change(array $item)
    {
        // TODO: Implement change() method.
    }
}
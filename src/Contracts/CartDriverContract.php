<?php
declare(strict_types=1);

namespace Cart\Contracts;

/**
 * Interface CartDriverContract
 */
interface CartDriverContract
{
    public function add(array $item);
    public function remove(int $id);
    public function clear();
    public function change(array $item);
}
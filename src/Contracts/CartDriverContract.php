<?php
declare(strict_types=1);

namespace Cart\Contracts;

/**
 * Interface CartDriverContract
 */
interface CartDriverContract
{
    /**
     * Add new item in cart
     * @param array $item
     * @return mixed
     */
    public function add(array $item) : bool ;

    /**
     * Remove item from ccart
     * @param int $id
     * @return mixed
     */
    public function remove(int $id):bool ;

    /**
     * Clear cart for concrete relation entity
     * @return mixed
     */
    public function clear():void;

    /**
     * Change item(position)
     * @param array $item
     * @return mixed
     */
    public function change(array $item);
}
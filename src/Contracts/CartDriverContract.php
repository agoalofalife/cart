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
     * Remove item from cart
     * @param array $itemId
     * @return mixed
     */
    public function remove(array $itemId):bool ;

    /**
     * Clear cart for concrete relation entity
     *
     * @param int $entityId
     * @return mixed|void
     */
    public function clear(int $entityId):void;

    /**
     * Change item(position)
     * @param array $item
     * @param CounterItemContract $itemContract
     * @return mixed
     */
    public function change(array $item, CounterItemContract $itemContract) : bool ;
}
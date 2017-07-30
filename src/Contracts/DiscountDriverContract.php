<?php
declare(strict_types=1);

namespace Cart\Contracts;

/**
 * Interface DiscountDriverContract
 * @package Cart\Contracts
 */
interface DiscountDriverContract
{
    /**
     * Make Discount
     * @param DiscountContract $contract
     * @param array $items
     * @return bool
     */
    public function discount(DiscountContract $contract, array $items) : bool ;
}
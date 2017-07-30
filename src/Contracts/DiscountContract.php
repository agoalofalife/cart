<?php
declare(strict_types=1);

namespace Cart\Contracts;

interface DiscountContract
{
    /**
     * Make Discount
     *
     * @param int $basePrice
     * @return int
     */
    public function make(int $basePrice) : int;
}
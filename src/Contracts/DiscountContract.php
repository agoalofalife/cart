<?php
declare(strict_types=1);

namespace Cart\Contracts;

interface DiscountContract
{
    /**
     * Make Discount
     *
     * @param int $basePrice
     * @return float
     */
    public function make(int $basePrice) : float;
}
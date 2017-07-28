<?php
declare(strict_types=1);

namespace Cart\DiscountStrategy;

use Cart\Contracts\DiscountContract;

class FixDiscountStrategy implements DiscountContract
{

    /**
     * Make Discount
     *
     * @param int $basePrice
     * @param     $relation
     * @return int
     */
    public function make(int $basePrice, $relation): int
    {
        return $basePrice - $relation;
    }
}
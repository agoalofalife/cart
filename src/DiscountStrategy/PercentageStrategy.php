<?php
declare(strict_types=1);

namespace Cart\DiscountStrategy;

use Cart\Contracts\DiscountContract;

class PercentageStrategy implements DiscountContract
{
    /**
     * Make Discount
     *
     * @param int $basePrice
     * @param     $percentage
     * @return int
     */
    public function make(int $basePrice, $percentage): int
    {
        return $basePrice / 100 * $percentage;
    }
}
<?php
declare(strict_types=1);

namespace Cart\DiscountStrategy;

use Cart\Contracts\DiscountContract;

class PercentageStrategy implements DiscountContract
{
    /**
     * @var int
     */
    protected $sign;

    public function __construct(int $sign)
    {
        $this->sign = $sign;
    }

    /**
     * Make Discount
     *
     * @param int $basePrice
     * @return int
     */
    public function make(int $basePrice): int
    {
        return $basePrice / 100 * $this->sign;
    }
}
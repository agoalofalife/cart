<?php
declare(strict_types=1);

namespace Cart\DiscountStrategy;

use Cart\Contracts\DiscountContract;

class FixDiscountStrategy implements DiscountContract
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
     * @return float
     */
    public function make(int $basePrice): float
    {
        $price = $basePrice - $this->sign;
        if ($price < 0) {
            $price =  0 ;
        }
        return $price;
    }
}
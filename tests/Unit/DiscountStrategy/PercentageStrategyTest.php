<?php
declare(strict_types=1);
namespace Cart\Tests\Unit\DiscountStrategy;

use Cart\DiscountStrategy\PercentageStrategy;
use Cart\Tests\TestCase;

class PercentageStrategyTest extends TestCase
{
    public function testMake() : void
    {
        $sign  = $this->faker()->randomDigit;
        $base  = $this->faker()->randomDigit;

        $class = new PercentageStrategy($sign);
        $result = ($base / 100 * $sign);
        $this->assertEquals($result, $class->make($base));
    }
}
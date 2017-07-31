<?php
declare(strict_types=1);
namespace Cart\Tests\Unit\DiscountStrategy;

use Cart\DiscountStrategy\FixDiscountStrategy;
use Cart\Tests\TestCase;

class FixDiscountStrategyTest extends TestCase
{
    public function testMakeGreaterZero() : void
    {
        $sign  = $this->faker()->randomDigit;
        $base  = $this->faker()->randomDigit;

        $class = new FixDiscountStrategy($sign);
        $result = ($base - $sign);
        $this->assertEquals($result < 0 ? 0 : $result, $class->make($base));
    }

    public function testMakeLessZero() : void
    {
        $sign  = $this->faker()->randomDigit;
        $base  = 0;

        $class = new FixDiscountStrategy($sign);
        $result = ($base - $sign);
        $this->assertEquals($result < 0 ? 0 : $result, $class->make($base));
    }
}
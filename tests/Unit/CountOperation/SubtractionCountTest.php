<?php
declare(strict_types=1);
namespace Cart\Tests\Unit\CountOperation;

use Cart\CountOperation\SubtractionCount;
use Cart\Tests\TestCase;

class SubtractionCountTest extends TestCase
{
    public function testExecute() : void
    {
        $counter = new SubtractionCount();
        $this->assertEquals(8, $counter->execute(10, 2));
        $this->assertEquals(0, $counter->execute(2, 10));
    }
}
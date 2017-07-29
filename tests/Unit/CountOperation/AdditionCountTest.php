<?php
declare(strict_types=1);
namespace Cart\Tests\Unit\CountOperation;

use Cart\CountOperation\AdditionCount;
use Cart\Tests\TestCase;

class AdditionCountTest extends TestCase
{
    public function testExecute() : void
    {
        $counter = new AdditionCount();
        $this->assertEquals(12, $counter->execute(10, 2));
    }
}
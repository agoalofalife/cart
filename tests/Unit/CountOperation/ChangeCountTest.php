<?php
declare(strict_types=1);

namespace Cart\Tests\Unit\CountOperation;

use Cart\CountOperation\ChangeCount;
use Cart\Tests\TestCase;

class ChangeCountTest extends TestCase
{
    public function testExecute() : void
    {
        $counter = new ChangeCount();
        $this->assertEquals(2, $counter->execute(10, 2));
        $this->assertEquals(0, $counter->execute(10, -2));
    }
}
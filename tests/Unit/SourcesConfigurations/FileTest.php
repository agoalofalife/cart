<?php
declare(strict_types=1);

namespace Cart\SourcesConfigurations;

use Cart\Tests\TestCase;

class FileTest extends TestCase
{
    public function testException() : void
    {
        $this->expectExceptionMessage('Local file name is not exist.');
        new File('');
    }

    public function testGet() : void
    {
        $file = new File(__DIR__.'/../../../config/cart.php');
        $this->assertEquals('cart', $file->getName());
        $this->assertInternalType('array', $file->get());
    }
}
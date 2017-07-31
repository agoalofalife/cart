<?php
declare(strict_types=1);
namespace Cart\Tests\Unit;

use Cart\Kernel;
use Cart\Tests\TestCase;
use Illuminate\Config\Repository;

class HelpersTest extends TestCase
{
    public function setUp()
    {
        app()->flush();
        $kernel = new Kernel();
        $kernel->bootstrapping();
    }

    public function testConfig() : void
    {
        $this->assertInstanceOf(Repository::class, config());
        config(['test']);
    }
}
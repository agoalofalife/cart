<?php
declare(strict_types=1);
namespace Cart\Tests\Unit;

use Cart\Contracts\CartDriverContract;
use Cart\Kernel;
use Cart\Tests\TestCase;

class KernelTest extends TestCase
{
    /**
     * @var Kernel
     */
    protected $kernel;

    public function setUp()
    {
        $this->kernel = new Kernel();
    }

    public function testBootstrapping() : void
    {
        $this->kernel->bootstrapping();
    }

    public function testGetStorage() : void
    {
        $this->assertInstanceOf(CartDriverContract::class, $this->kernel->getStorage());
        app()->flush();
    }
}
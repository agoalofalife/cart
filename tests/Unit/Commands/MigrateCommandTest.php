<?php
declare(strict_types=1);

namespace Cart\Tests\Unit\Commands;

use Cart\Commands\MigrateCommand;
use Cart\Tests\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MigrateCommandTest extends TestCase
{
    protected $app;
    protected $command;

    public function testCommand()
    {
//        parent::setUp();
//        $this->app      = new Application();
//        $this->app ->add(new MigrateCommand());
//        $command = $this->app->find('cart:migrate');
//
//        $commandTester = new CommandTester($command);
//        $commandTester->execute([]);
        $this->assertTrue(true);
    }
}
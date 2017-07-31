<?php
declare(strict_types=1);
namespace Cart\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class MigrateRollbackCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cart:migrate:rollback')
            ->setDescription('Deletes tables cart.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (Finder::create()->files()->name('*.php')
                     ->in(__DIR__. '/../../migrations') as $file) {
            $classes = get_declared_classes();
            include $file->getRealPath();
            $diff = array_diff(get_declared_classes(), $classes);
            $class = reset($diff);
            (new $class())->down();
            $output->writeln('<fg=green>Success rollback migration: ' . basename($file->getFilename(), '.php') .'</>');
        }
    }
}
<?php
declare(strict_types=1);

namespace Cart\Commands;

use Cart\Drivers\DatabaseDriver;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class MigrateCommand
 *
 * @package Cart\Commands
 */
class MigrateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cart:migration')
            ->setDescription('Creates a new table cart.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (Finder::create()->files()->name('*.php')->in(__DIR__. '/../../migrations') as $file) {
            $classes = get_declared_classes();
            require_once $file->getRealPath();
        }
//        $file = implode('_', array_slice(explode('_', $file), 4));
//
//        $class = Str::studly($file);
//
//        return new $class;
//        dd(app(DatabaseDriver::class));

        $classes = get_declared_classes();
        dd($classes);
//        include '';
//        $diff = array_diff(get_declared_classes(), $classes);
//        $class = reset($diff);
    }
}
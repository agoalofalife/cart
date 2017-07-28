<?php
declare(strict_types=1);

namespace Cart\CountOperation;

use Cart\Contracts\CounterItemContract;

/**
 * Class AdditionCount
 *
 * @package CountOperation
 */
class AdditionCount implements CounterItemContract
{
    /**
     * Operation
     * @param int $before
     * @param int $howMuch
     * @return int
     */
    public function execute(int $before, int $howMuch) : int
    {
        return $before + $howMuch;
    }
}
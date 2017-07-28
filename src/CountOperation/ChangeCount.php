<?php
declare(strict_types=1);

namespace Cart\CountOperation;

use Cart\Contracts\CounterItemContract;

/**
 * Class ChangeCount
 *
 * @package CountOperation
 */
class ChangeCount implements CounterItemContract
{

    /**
     * Operation
     *
     * @param int $before
     * @param int $howMuch
     * @return int
     */
    public function execute(int $before, int $howMuch): int
    {
        $before = $howMuch;
        $before < 0 ? $before = 0 : true;
        return $before;
    }
}
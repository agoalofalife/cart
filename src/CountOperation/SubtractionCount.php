<?php
declare(strict_types=1);

namespace Cart\CountOperation;

use Cart\Contracts\CounterItemContract;

/**
 * Class SubtractionCount
 *
 * @package CountOperation
 */
class SubtractionCount implements CounterItemContract
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
        $value = $before - $howMuch;
        $value < 0 ? $value = 0 : true;
        return $value;
    }
}
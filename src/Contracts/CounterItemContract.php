<?php
declare(strict_types=1);

namespace Cart\Contracts;

/**
 * Interface CounterItemContract
 *
 * @package Cart\Contracts
 */
interface CounterItemContract
{
    /**
     * Operation
     * @param int $before
     * @param int $howMuch
     * @return int
     */
    public function execute(int $before, int $howMuch) : int;
}
<?php
declare(strict_types=1);

namespace Cart\Contracts;

/**
 * Interface SetTableDriver
 *
 * @package Cart\Contracts
 */
interface SetTableDriver
{
    public function setTable(string $name) : void;
}
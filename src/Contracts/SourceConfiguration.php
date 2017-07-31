<?php
declare(strict_types=1);

namespace Cart\Contracts;

/**
 * Interface SourceConfiguration
 *
 * @package Cart\Contracts
 */
interface SourceConfiguration
{
    /**
     * @return array
     */
    public function get() : array;

    /**
     * Get name
     * @return string
     */
    public function getName() : string;
}
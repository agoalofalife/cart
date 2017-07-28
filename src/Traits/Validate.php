<?php
declare(strict_types=1);

namespace Cart\Traits;

trait Validate
{
    /**
     * Validate input parameters
     *
     * @param array $item
     * @param array $require
     * @return bool
     * @internal param array $required
     */
    private function validate(array $item, array $require = []) : bool
    {
        foreach ($require as $parameter) {
            if (isset($item[$parameter]) === false) {
                return false;
            }
        }
        return true;
    }
}
<?php
declare(strict_types=1);

namespace Cart\Contracts;

use Illuminate\Container\Container;

/**
 * Interface ServiceProviderContract
 *
 * @package Cart\Contracts
 */
interface ServiceProviderContract
{
    public function register(Container $container);
}
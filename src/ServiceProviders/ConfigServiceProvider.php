<?php
declare(strict_types=1);

namespace Cart\ServiceProviders;

use Cart\Contracts\ServiceProviderContract;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;

class ConfigServiceProvider implements ServiceProviderContract
{
    public function register(Container $container)
    {
        if (app()->resolved('config') === false) {
            app()->instance('config', new Repository());
        }
    }
}
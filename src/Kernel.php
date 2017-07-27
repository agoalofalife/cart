<?php
declare(strict_types=1);

namespace Cart;

use Cart\Contracts\ServiceProviderContract;
use Illuminate\Container\Container;

class Kernel
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var ServiceProviderContract
     */
    protected $services;

    public function __construct()
    {
        $this->app = new Container();
    }

    public function addService(ServiceProviderContract $service)
    {
        $this->services[] = $service;
    }

    public function bootstrapping()
    {
        foreach ($this->services as $service) {
            $service->register();
        }
    }
}
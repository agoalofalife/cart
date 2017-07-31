<?php
declare(strict_types=1);

namespace Cart;

use Cart\Contracts\CartDriverContract;
use Cart\Contracts\ServiceProviderContract;
use Cart\Contracts\SourceConfiguration;
use Illuminate\Container\Container;
use Illuminate\Config\Repository;
use Symfony\Component\Finder\Finder;

/**
 * Class Kernel
 *
 * @package Cart
 */
class Kernel
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var string
     */
    protected $configPath = __DIR__. '/../config';
    /**
     * @var CartDriverContract
     */
    protected $currentDriver;

    protected $coreServices = [
        'config.singleton' => Repository::class,
    ];

    public function __construct()
    {
        $this->app = Container::getInstance();
    }

    public function bootstrapping() : void
    {
        $this->loadCoreServiceProvider();
    }

    /**
     * Get type storage (Note: in config file)
     */
    public function getStorage() : CartDriverContract
    {
        return $this->app->make(config('cart.storage'));
    }

    /**
     * Get all of the configuration files for the application.
     * @param SourceConfiguration $configuration
     */
    public function loadConfiguration(SourceConfiguration $configuration) :  void
    {
        config()->set($configuration->getName(), $configuration->get());
        $this->loadServiceProvider();
    }

    /**
     * Load mandatory services for application
     */
    protected function loadCoreServiceProvider() : void
    {
        foreach ($this->coreServices as $abstract => $service) {
            list($abstract, $type) = explode('.', $abstract);

            if (app()->resolved($abstract) === false) {
                if ($type == 'singleton') {
                    $this->app->singleton($abstract, $service);
                }
            }
        }
    }

    /**
     * Load service provider from config file
     */
    protected function loadServiceProvider() : void
    {
        foreach (config('cart.services') as $services) {
            /** @var ServiceProviderContract */
            (new $services)->register($this->app);
        }
    }
}

<?php
declare(strict_types=1);

namespace Cart;

use Cart\Contracts\CartDriverContract;
use Cart\Contracts\ServiceProviderContract;
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
        $this->loadConfigurationFiles();
        $this->loadServiceProvider();
    }

    /**
     * Get type storage (Note: in config file)
     */
    public function getStorage() : CartDriverContract
    {
        return $this->app->make(config('app.storage'));
    }

    /**
     * Load configuration from config folder
     */
    protected function loadConfigurationFiles() : void
    {
        $repository = $this->app->make('config');

        foreach ($this->getConfigurationFiles() as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     * @return array
     */
    protected function getConfigurationFiles() : array
    {
        $files = [];

        $configPath = realpath($this->configPath);

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $files[basename($file->getFilename(), '.php')] = $file->getRealPath();
        }

        return $files;
    }

    /**
     * Load mandatory services for application
     */
    protected function loadCoreServiceProvider() : void
    {
        foreach ($this->coreServices as $abstract => $service) {
            list($abstract, $type) = explode('.', $abstract);

            if ($type == 'singleton') {
                $this->app->singleton($abstract, $service);
            } else {
                $this->app->bind($abstract, $service);
            }
        }
    }

    /**
     * Load service provider from config file
     */
    protected function loadServiceProvider() : void
    {
        foreach (config('app.services') as $services) {
            /** @var ServiceProviderContract */
            (new $services)->register($this->app);
        }
    }
}

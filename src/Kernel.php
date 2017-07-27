<?php
declare(strict_types=1);

namespace Cart;

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
     * @var ServiceProviderContract
     */
    protected $services;

    /**
     * @var string
     */
    protected $configPath = __DIR__. '/../config';

    protected $coreServices = [
        Repository::class,
    ];

    public function __construct(string $basePath = null)
    {
        $this->app = Container::getInstance();
    }

    public function addService(ServiceProviderContract $service) : void
    {
        $this->services[] = $service;
    }

    public function bootstrapping() : void
    {
        foreach ($this->coreServices as $service) {
            $this->app->bind($service);
        }
        $this->loadConfigurationFiles();
    }

    /**
     * Load configuration from config folder
     */
    protected function loadConfigurationFiles() : void
    {
        $repository = app(Repository::class);

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
}

<?php
declare(strict_types=1);

namespace Cart\ServiceProviders;

use Cart\Kernel;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('cart', function () {
            $kernel = new Kernel();
            $kernel->bootstrapping();
            $kernel->loadServiceProvider();
            return $kernel->getStorage();
        });
    }
}
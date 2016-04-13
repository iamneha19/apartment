<?php

namespace Packages\Billing;

use Illuminate\Support\ServiceProvider;

/**
 * Billing provider
 *
 * @author Mohammed Mudasir
 */
class BillingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        require __DIR__.'/routes.php';

        $this->loadViewsFrom(__DIR__.'/views', 'Billing');
    }

    public function register()
    {
        # code...
    }
}

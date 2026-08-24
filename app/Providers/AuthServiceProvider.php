<?php

namespace App\Providers;

use App\Auth\StaffUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Auth::provider('staff', function ($app, array $config) {
            return new StaffUserProvider($app['hash'], $config['model']);
        });

        // Define gates for IDE recognition (actual logic is in AppServiceProvider's Gate::before)
        Gate::define('manage_catalog', fn() => true);
        Gate::define('manage_customers', fn() => true);
        Gate::define('manage_staff', fn() => true);
        Gate::define('manage_stores', fn() => true);
    }
}

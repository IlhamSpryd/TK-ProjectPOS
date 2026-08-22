<?php

namespace App\Providers;

use App\Auth\StaffUserProvider;
use Illuminate\Support\Facades\Auth;
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

    }
}

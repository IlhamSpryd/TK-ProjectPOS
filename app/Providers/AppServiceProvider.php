<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Set DB context just before login to allow RLS policies to pass
        // when Laravel automatically updates remember_token or rehashes password.
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Validated::class, function (\Illuminate\Auth\Events\Validated $event) {
            if (DB::connection()->getDriverName() === 'pgsql') {
                $staff = $event->user;
                if ($staff instanceof \App\Models\Staff) {
                    DB::statement("SET app.staff_id = '{$staff->id}'");
                    DB::statement("SET app.current_tenant_id = '{$staff->tenant_id}'");
                }
            }
        });

        // RBAC: Secara otomatis meloloskan Super Admin dan mengecek permission dari database
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}

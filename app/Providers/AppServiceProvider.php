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
        // Cegah penggunaan transaction pooler (port 6543) di production
        // karena variabel sesi PostgreSQL bisa bocor antar-request
        if (app()->isProduction() && config('database.connections.pgsql.port') == 6543) {
            throw new \RuntimeException(
                'FATAL: DB_PORT=6543 (transaction pooler) terdeteksi di production. '
                . 'Gunakan session pooler (port 5432) untuk mencegah kebocoran variabel sesi RLS antar-request.'
            );
        }

        $this->configureDefaults();

        // Set DB context just before login to allow RLS policies to pass
        // when Laravel automatically updates remember_token or rehashes password.
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Validated::class, function (\Illuminate\Auth\Events\Validated $event) {
            if (DB::connection()->getDriverName() === 'pgsql') {
                $staff = $event->user;
                if ($staff instanceof \App\Models\Staff) {
                    DB::select('SELECT set_config(?, ?, false)', ['app.staff_id', (string) $staff->id]);
                    DB::select('SELECT set_config(?, ?, false)', ['app.current_tenant_id', (string) $staff->tenant_id]);
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

        $this->bootObservers();
    }

    /**
     * Bootstrap model observers for event-driven cache invalidation.
     */
    private function bootObservers(): void
    {
        $invalidateCatalog = function ($model) {
            if (isset($model->tenant_id)) {
                $stores = \App\Models\Store::where('tenant_id', $model->tenant_id)->pluck('id');
                $catalog = app(\App\Services\ProductCatalogService::class);
                foreach ($stores as $storeId) {
                    $catalog->invalidateCatalog($storeId);
                }
            }
        };

        $invalidateInventory = function ($model) {
            if (isset($model->store_id)) {
                app(\App\Services\ProductCatalogService::class)->invalidateCatalog($model->store_id);
            }
        };

        \App\Models\Product::saved($invalidateCatalog);
        \App\Models\Product::deleted($invalidateCatalog);
        \App\Models\ProductVariant::saved($invalidateCatalog);
        \App\Models\ProductVariant::deleted($invalidateCatalog);
        \App\Models\InventoryStock::saved($invalidateInventory);
        \App\Models\InventoryStock::deleted($invalidateInventory);

        $invalidateCategory = function ($model) {
            if (isset($model->tenant_id)) {
                app(\App\Services\ProductCatalogService::class)->invalidateCategories($model->tenant_id);
            }
        };
        \App\Models\Category::saved($invalidateCategory);
        \App\Models\Category::deleted($invalidateCategory);

        $invalidateTax = function ($model) {
            if (isset($model->tenant_id)) {
                app(\App\Services\ProductCatalogService::class)->invalidateTaxRates($model->tenant_id);
            }
        };
        \App\Models\TaxCategory::saved($invalidateTax);
        \App\Models\TaxCategory::deleted($invalidateTax);
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

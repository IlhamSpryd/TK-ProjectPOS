<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait BelongsToTenant
 *
 * Menerapkan Global Scope untuk memisahkan data antar tenant (multi-tenancy).
 * Secara otomatis menambahkan `tenant_id` ke query dan saat pembuatan data.
 */
trait BelongsToTenant
{
    /**
     * Boot trait untuk mendaftarkan global scope dan event model.
     */
    protected static function bootBelongsToTenant()
    {
        // DEFENSE-IN-DEPTH: Filter SEMUA query (SELECT/UPDATE/DELETE) berdasarkan tenant
        // di layer aplikasi, selain perlindungan dari RLS Postgres.
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check()) {
                $tenantId = Auth::user()->tenant_id ?? null;

                if ($tenantId) {
                    $builder->where(
                        $builder->getModel()->getTable().'.tenant_id',
                        $tenantId
                    );
                }
            }
        });

        // Otomatis isi tenant_id saat pembuatan record baru
        static::creating(function ($model) {
            if (empty($model->tenant_id) && Auth::check()) {
                $staff = Auth::user();
                if ($staff && ! empty($staff->tenant_id)) {
                    $model->tenant_id = $staff->tenant_id;
                }
            }
        });
    }

    /**
     * Escape hatch eksplisit untuk query lintas tenant (misal: pelaporan global admin super).
     * Harus dipanggil secara sadar, tidak berlaku secara default.
     */
    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}

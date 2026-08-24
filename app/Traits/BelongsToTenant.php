<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // DEFENSE-IN-DEPTH: scope SEMUA query (SELECT/UPDATE/DELETE) per tenant
        // di layer aplikasi, tidak hanya mengandalkan RLS Postgres.
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
     * Escape hatch eksplisit untuk kasus admin lintas tenant (mis. reporting global).
     * Harus dipanggil sadar, tidak default.
     */
    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}

<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    /**
     * Boot the trait to auto-assign tenant_id on model creation.
     */
    protected static function bootBelongsToTenant()
    {
        static::creating(function ($model) {
            if (empty($model->tenant_id) && Auth::check()) {
                $staff = Auth::user();
                if ($staff && !empty($staff->tenant_id)) {
                    $model->tenant_id = $staff->tenant_id;
                }
            }
        });
    }
}

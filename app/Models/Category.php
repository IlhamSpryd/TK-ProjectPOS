<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    use \App\Traits\BelongsToTenant;

    protected $table = 'categories';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'active',
    ];

    protected $casts = [
        'id' => 'string',
        'active' => 'boolean',
    ];

    public static function activeCached(): Collection
    {
        return self::where('active', true)->orderBy('name')->get();
    }


}

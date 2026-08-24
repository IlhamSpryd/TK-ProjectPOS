<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TaxCategory extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToTenant;

    protected $table = 'tax_categories';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'name',
        'tax_type',
        'rate',
        'active',
    ];

    protected $casts = [
        'id' => 'string',
        'rate' => 'decimal:2',
        'active' => 'boolean',
    ];

    public static function activeCached(): Collection
    {
        return self::where('active', true)->orderBy('name')->get();
    }
}

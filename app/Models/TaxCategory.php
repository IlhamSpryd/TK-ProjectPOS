<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCategory extends Model
{
    use HasFactory;

    protected $table = 'tax_categories';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'name',
        'tax_type',
        'rate',
        'active'
    ];

    protected $casts = [
        'id' => 'string',
        'rate' => 'decimal:2',
        'active' => 'boolean'
    ];

    protected static $activeCache = null;

    public static function activeCached(): \Illuminate\Support\Collection
    {
        if (self::$activeCache === null) {
            self::$activeCache = self::where('active', true)->orderBy('name')->get();
        }
        return self::$activeCache;
    }

    public static function clearActiveCache(): void
    {
        self::$activeCache = null;
    }
}

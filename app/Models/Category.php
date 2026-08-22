<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'parent_id',
        'name',
        'active',
    ];

    protected $casts = [
        'id' => 'string',
        'active' => 'boolean',
    ];

    protected static $activeCache = null;

    public static function activeCached(): Collection
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

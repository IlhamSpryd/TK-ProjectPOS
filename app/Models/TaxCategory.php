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

}

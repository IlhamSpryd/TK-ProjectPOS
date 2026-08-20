<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'business_type',
        'is_pkp',
        'npwp',
        'address',
        'city',
        'province',
        'phone',
        'email',
        'currency',
        'timezone',
        'active',
        'default_tax_category_id'
    ];

    protected $casts = [
        'id' => 'string',
        'is_pkp' => 'boolean',
        'active' => 'boolean'
    ];

    public function staff() { return $this->belongsToMany(Staff::class, 'staff_stores')->withPivot('is_primary'); }
}

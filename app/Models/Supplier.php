<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'active'
    ];

    protected $casts = [
        'id' => 'string',
        'active' => 'boolean'
    ];

}

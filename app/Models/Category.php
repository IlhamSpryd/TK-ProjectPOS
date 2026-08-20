<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'parent_id',
        'name',
        'active'
    ];

    protected $casts = [
        'id' => 'string',
        'active' => 'boolean'
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    use HasFactory;

    protected $table = 'registers';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'store_id',
        'name',
        'status',
        'active'
    ];

    protected $casts = [
        'id' => 'string',
        'active' => 'boolean'
    ];

    public function store() { return $this->belongsTo(Store::class); }
}

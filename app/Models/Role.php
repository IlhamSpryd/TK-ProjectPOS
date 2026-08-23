<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToTenant;

    protected $table = 'roles';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'name',
        'permissions',
    ];

    protected $casts = [
        'id' => 'string',
        'permissions' => 'array',
    ];
}

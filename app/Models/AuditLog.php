<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_data',
        'new_data',
        'changed_by'
    ];

    protected $casts = [
        'id' => 'string',
        'record_id' => 'string',
        'old_data' => 'array',
        'new_data' => 'array',
        'changed_at' => 'datetime',
    ];
}

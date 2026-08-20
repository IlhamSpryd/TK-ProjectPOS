<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;

    protected $table = 'sale_returns';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'staff_id',
        'return_date',
        'total_refund',
        'reason'
    ];

    protected $casts = [
        'id' => 'string',
        'return_date' => 'datetime',
        'total_refund' => 'decimal:2'
    ];

    public function sale() { return $this->belongsTo(Sale::class); }
    public function staff() { return $this->belongsTo(Staff::class); }
    public function items() { return $this->hasMany(SaleReturnItem::class); }
}

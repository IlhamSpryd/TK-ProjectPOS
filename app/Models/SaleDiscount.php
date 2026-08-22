<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDiscount extends Model
{
    protected $table = 'sale_discounts';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'discount_id',
        'label',
        'discount_type',
        'value',
        'amount_applied',
    ];

    protected $casts = [
        'id'             => 'string',
        'value'          => 'decimal:4',
        'amount_applied' => 'decimal:4',
        'created_at'     => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}

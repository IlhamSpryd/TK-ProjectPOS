<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_items';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'purchase_order_id',
        'variant_id',
        'quantity',
        'cost_price',
        'discount'
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount' => 'decimal:2'
    ];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}

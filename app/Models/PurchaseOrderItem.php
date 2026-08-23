<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToTenant;

    protected $table = 'purchase_order_items';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'purchase_order_id',
        'variant_id',
        'quantity',
        'cost_price',
        'discount',
        'received_quantity',
        'received',
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'received' => 'boolean',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}

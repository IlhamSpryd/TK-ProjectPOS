<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToTenant;

    protected $table = 'sale_items';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'sale_id',
        'product_id',
        'product_name',
        'variant_id',
        'variant_sku',
        'variant_attributes',
        'quantity',
        'unit',
        'unit_price',
        'cost_price',
        'discount',
        'tax_category_id',
        'tax_name',
        'tax_rate',
        'tax_amount',
        'modifiers',
    ];

    protected $casts = [
        'id' => 'string',
        'variant_attributes' => 'array',
        'modifiers' => 'array',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function taxCategory()
    {
        return $this->belongsTo(TaxCategory::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $table = 'sale_items';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'variant_id',
        'quantity',
        'unit_price',
        'cost_price',
        'discount',
        'tax_category_id',
        'tax_amount'
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2'
    ];

    public function sale() { return $this->belongsTo(Sale::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
    public function taxCategory() { return $this->belongsTo(TaxCategory::class); }
    public function discount() { return $this->belongsTo(Discount::class); }
}

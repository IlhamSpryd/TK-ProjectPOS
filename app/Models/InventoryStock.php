<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use HasFactory;

    protected $table = 'inventory_stock';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'variant_id',
        'store_id',
        'quantity',
        'reorder_point'
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'decimal:2',
        'reorder_point' => 'decimal:2'
    ];

    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
    public function store() { return $this->belongsTo(Store::class); }
}

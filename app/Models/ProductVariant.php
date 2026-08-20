<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_variants';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'attributes',
        'cost_price',
        'selling_price',
        'active'
    ];

    protected $casts = [
        'id' => 'string',
        'attributes' => 'array',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'active' => 'boolean'
    ];

    public function product() { return $this->belongsTo(Product::class); }
}

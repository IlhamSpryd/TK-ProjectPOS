<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'tax_category_id',
        'name',
        'description',
        'image_url',
        'unit',
        'sku',
        'track_stock',
        'is_service',
        'active'
    ];

    protected $casts = [
        'id' => 'string',
        'track_stock' => 'boolean',
        'is_service' => 'boolean',
        'active' => 'boolean'
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function taxCategory() { return $this->belongsTo(TaxCategory::class); }
    public function variants() { return $this->hasMany(ProductVariant::class, 'product_id'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'inventory_movements';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'variant_id',
        'store_id',
        'movement_type',
        'quantity_change',
        'reference_table',
        'reference_id',
        'note',
        'staff_id'
    ];

    protected $casts = [
        'id' => 'string',
        'quantity_change' => 'decimal:2'
    ];

    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
    public function store() { return $this->belongsTo(Store::class); }
    public function staff() { return $this->belongsTo(Staff::class); }
}

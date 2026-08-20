<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'store_id',
        'supplier_id',
        'staff_id',
        'order_date',
        'reference_no',
        'status',
        'subtotal',
        'discount_total',
        'tax_total',
        'grand_total',
        'notes'
    ];

    protected $casts = [
        'id' => 'string',
        'order_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2'
    ];

    public function store() { return $this->belongsTo(Store::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function staff() { return $this->belongsTo(Staff::class); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
}

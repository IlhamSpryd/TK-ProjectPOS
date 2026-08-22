<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'store_id',
        'customer_id',
        'staff_id',
        'register_id',
        'shift_id',
        'sale_number',
        'sale_date',
        'status',
        'subtotal',
        'discount_total',
        'tax_total',
        'service_charge_total',
        'grand_total',
        'payment_status',
        'void_reason',
        'voided_by',
        'voided_at',
        'notes'
    ];

    protected $casts = [
        'id' => 'string',
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'service_charge_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'voided_at' => 'datetime'
    ];

    public function store() { return $this->belongsTo(Store::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function staff() { return $this->belongsTo(Staff::class); }
    public function register() { return $this->belongsTo(Register::class); }
    public function shift() { return $this->belongsTo(Shift::class); }
    public function items() { return $this->hasMany(SaleItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function discounts() { return $this->hasMany(SaleDiscount::class); }
    public function returns() { return $this->hasMany(SaleReturn::class); }
    public function loyaltyLedger() { return $this->hasMany(LoyaltyLedger::class); }
}


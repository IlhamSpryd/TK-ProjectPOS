<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyLedger extends Model
{
    protected $table = 'loyalty_ledger';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'sale_id',
        'points_change',
        'description',
    ];

    protected $casts = [
        'id'            => 'string',
        'points_change' => 'integer',
        'created_at'    => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}

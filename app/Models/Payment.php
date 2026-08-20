<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'payment_method',
        'amount',
        'change_amount',
        'reference_no',
        'paid_at'
    ];

    protected $casts = [
        'id' => 'string',
        'amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function sale() { return $this->belongsTo(Sale::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToTenant;

    protected $table = 'expenses';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'store_id',
        'staff_id',
        'category',
        'amount',
        'description',
        'expense_date',
    ];

    protected $casts = [
        'id' => 'string',
        'amount' => 'decimal:2',
        'expense_date' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}

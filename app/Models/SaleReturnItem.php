<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToTenant;

    protected $table = 'sale_return_items';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'sale_return_id',
        'sale_item_id',
        'quantity',
        'refund_amount',
        'restock',
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'restock' => 'boolean',
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }
}

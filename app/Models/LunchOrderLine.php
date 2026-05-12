<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LunchOrderLine extends Model
{
    use HasFactory;

    protected $fillable = ['lunch_order_id', 'product_id', 'quantity', 'unit_price', 'subtotal'];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(LunchOrder::class, 'lunch_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(LunchProduct::class, 'product_id');
    }
}

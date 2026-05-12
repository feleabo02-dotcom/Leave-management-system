<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_order_id', 'description', 'product_id',
        'quantity', 'cost', 'price', 'type',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class, 'repair_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

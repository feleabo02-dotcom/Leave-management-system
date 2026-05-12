<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLot extends Model
{
    protected $fillable = [
        'name', 'ref', 'product_id', 'company_id', 'notes',
        'expiration_date', 'best_before_date', 'removal_date', 'alert_date',
    ];

    protected $casts = [
        'expiration_date' => 'datetime',
        'best_before_date' => 'datetime',
        'removal_date' => 'datetime',
        'alert_date' => 'datetime',
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}

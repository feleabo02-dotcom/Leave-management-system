<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'product_id', 'customer_id', 'assigned_to', 'diagnosis',
        'status', 'priority', 'date_requested', 'date_scheduled',
        'date_completed', 'internal_notes', 'customer_notes',
    ];

    protected $casts = [
        'date_requested' => 'date',
        'date_scheduled' => 'date',
        'date_completed' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RepairLine::class, 'repair_order_id');
    }
}

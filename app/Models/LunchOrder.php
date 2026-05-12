<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LunchOrder extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'order_date', 'total', 'status', 'notes'];

    protected $casts = [
        'order_date' => 'date',
        'total' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(LunchOrderLine::class, 'lunch_order_id');
    }
}

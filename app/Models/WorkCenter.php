<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkCenter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'warehouse_id', 'capacity', 'hourly_cost', 'status'];

    protected $casts = [
        'hourly_cost' => 'decimal:2',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}

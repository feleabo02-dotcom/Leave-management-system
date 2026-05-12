<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutingStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'routing_id', 'work_center_id', 'sequence', 'name', 'hours', 'is_active',
    ];

    protected $casts = [
        'hours' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function routing(): BelongsTo
    {
        return $this->belongsTo(Routing::class, 'routing_id');
    }

    public function workCenter(): BelongsTo
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id');
    }
}

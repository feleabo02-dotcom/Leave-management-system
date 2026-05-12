<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Routing extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'bom_id', 'lead_time'];

    protected $casts = [
        'lead_time' => 'decimal:2',
    ];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RoutingStep::class, 'routing_id');
    }
}

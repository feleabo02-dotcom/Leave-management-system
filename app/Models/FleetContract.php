<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FleetContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id', 'type', 'name', 'provider', 'ref_number',
        'start_date', 'end_date', 'cost', 'cost_frequency', 'terms', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(FleetVehicle::class, 'vehicle_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FleetServiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id', 'type', 'description', 'date', 'cost',
        'odometer', 'vendor_id', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2',
        'odometer' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(FleetVehicle::class, 'vehicle_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}

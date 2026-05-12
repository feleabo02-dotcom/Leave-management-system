<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FleetVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'model_id', 'license_plate', 'driver_id', 'acquisition_date',
        'acquisition_cost', 'color', 'vin_number', 'seats', 'status',
        'current_odometer', 'company_id', 'notes',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'current_odometer' => 'decimal:2',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(FleetVehicleModel::class, 'model_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(FleetContract::class, 'vehicle_id');
    }

    public function serviceLogs(): HasMany
    {
        return $this->hasMany(FleetServiceLog::class, 'vehicle_id');
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FleetFuelLog::class, 'vehicle_id');
    }
}

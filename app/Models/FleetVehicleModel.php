<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FleetVehicleModel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'brand_id'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(FleetVehicleBrand::class, 'brand_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(FleetVehicle::class, 'model_id');
    }
}

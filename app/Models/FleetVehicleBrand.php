<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FleetVehicleBrand extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function models(): HasMany
    {
        return $this->hasMany(FleetVehicleModel::class, 'brand_id');
    }
}

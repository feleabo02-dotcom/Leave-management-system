<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceEquipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category_id', 'code', 'location_id', 'technician_id',
        'status', 'purchase_date', 'purchase_cost', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaintenanceEquipmentCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'location_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'technician_id');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'equipment_id');
    }
}

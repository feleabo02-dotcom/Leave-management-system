<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenancePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'equipment_id', 'interval_type', 'interval_count',
        'planned_date', 'last_executed', 'status', 'notes',
    ];

    protected $casts = [
        'planned_date' => 'date',
        'last_executed' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(MaintenanceEquipment::class, 'equipment_id');
    }
}

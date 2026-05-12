<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'equipment_id', 'description', 'requested_by', 'assigned_to',
        'priority', 'stage', 'category', 'scheduled_date', 'closed_date', 'resolution',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'closed_date' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(MaintenanceEquipment::class, 'equipment_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}

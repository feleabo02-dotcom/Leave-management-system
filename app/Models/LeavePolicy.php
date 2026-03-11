<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeavePolicy extends Model
{
    protected $fillable = [
        'leave_type_id',
        'version',
        'min_service_months',
        'max_days_per_year',
        'max_unpaid_days_per_year',
        'allow_backdate',
        'allow_future_apply_days',
        'yearly_reset',
        'expiry_days',
        'carry_forward_limit',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'max_days_per_year' => 'decimal:2',
        'max_unpaid_days_per_year' => 'decimal:2',
        'carry_forward_limit' => 'decimal:2',
        'allow_backdate' => 'boolean',
        'yearly_reset' => 'boolean',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}

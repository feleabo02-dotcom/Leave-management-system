<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'color',
        'is_paid',
        'requires_manager_approval',
        'requires_hr_approval',
        'carry_forward',
        'carry_forward_cap',
        'max_days_per_request',
        'active',
        'allocation_type',
        'validation_type',
        'request_unit',
        'allow_half_day',
        'allow_hour',
        'accrual_rate',
        'accrual_cap',
        'requires_allocation',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'requires_manager_approval' => 'boolean',
        'requires_hr_approval' => 'boolean',
        'carry_forward' => 'boolean',
        'active' => 'boolean',
        'allow_half_day' => 'boolean',
        'allow_hour' => 'boolean',
        'requires_allocation' => 'boolean',
        'accrual_rate' => 'decimal:2',
        'accrual_cap' => 'decimal:2',
    ];

    public function allocations(): HasMany
    {
        return $this->hasMany(LeaveAllocation::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function policies(): HasMany
    {
        return $this->hasMany(LeavePolicy::class);
    }
}

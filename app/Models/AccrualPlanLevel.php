<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccrualPlanLevel extends Model
{
    protected $fillable = [
        'accrual_plan_id',
        'sequence',
        'name',
        'added_value',
        'added_value_type',
        'frequency',
        'first_day',
        'first_month',
        'cap_accrued_time',
        'cap_accrued_time_amount',
        'cap_accrued_time_yearly',
        'cap_accrued_time_yearly_amount',
        'action_with_unused_accruals',
        'carryover_options',
        'carryover_limit_days',
        'accrual_validity_days',
    ];

    protected $casts = [
        'cap_accrued_time' => 'boolean',
        'cap_accrued_time_yearly' => 'boolean',
        'added_value' => 'decimal:2',
        'cap_accrued_time_amount' => 'decimal:2',
        'cap_accrued_time_yearly_amount' => 'decimal:2',
    ];

    public function accrualPlan(): BelongsTo
    {
        return $this->belongsTo(AccrualPlan::class);
    }
}

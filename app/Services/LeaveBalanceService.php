<?php

namespace App\Services;

use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LeaveBalanceService
{
    public function getHoursPerDay(): float
    {
        $setting = SystemSetting::query()->where('key', '=', 'hours_per_day')->first();
        $value = $setting?->value;

        if (is_array($value) && isset($value['hours'])) {
            return (float) $value['hours'];
        }

        return 8.0;
    }

    public function calculateBusinessDays(Carbon $start, Carbon $end): float
    {
        $period = CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay());
        $days = 0;

        foreach ($period as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            $days++;
        }

        return (float) $days;
    }

    public function calculateRequestedDays(Carbon $start, Carbon $end, string $unit, ?float $hours = null): float
    {
        if ($unit === 'half_day') {
            return 0.5;
        }

        if ($unit === 'hour') {
            $hoursPerDay = $this->getHoursPerDay();

            return $hours ? (float) round($hours / $hoursPerDay, 2) : 0.0;
        }

        return $this->calculateBusinessDays($start, $end);
    }

    public function getLeaveYearStart(): Carbon
    {
        $setting = SystemSetting::query()->where('key', '=', 'leave_year_start')->first();
        $value = $setting?->value;

        if (is_array($value) && isset($value['month'], $value['day'])) {
            return Carbon::create(null, (int) $value['month'], (int) $value['day']);
        }

        return Carbon::create(null, 1, 1);
    }

    public function getAllocation(User $user, LeaveType $leaveType, int $year): ?LeaveAllocation
    {
        return LeaveAllocation::query()
            ->where('user_id', '=', $user->id)
            ->where('leave_type_id', '=', $leaveType->id)
            ->where('year', '=', $year)
            ->first();
    }

    public function getRemaining(User $user, LeaveType $leaveType, int $year): float
    {
        $allocation = $this->getAllocation($user, $leaveType, $year);

        if (!$allocation && $leaveType->requires_allocation) {
            return 0.0;
        }

        $allocated = $allocation?->allocated_days ?? 0;
        $carried = $allocation?->carried_over_days ?? 0;

        if ($leaveType->allocation_type === 'accrual') {
            $allocated = $this->calculateAccruedDays($leaveType);
        }

        $total = $allocated + $carried;
        $used = $allocation?->used_days ?? 0;

        return (float) max(0, $total - $used);
    }

    public function calculateAccruedDays(LeaveType $leaveType): float
    {
        if ($leaveType->accrual_rate <= 0) {
            return 0.0;
        }

        $start = $this->getLeaveYearStart();
        $months = max(1, $start->diffInMonths(now()->startOfMonth()) + 1);
        $accrued = $months * $leaveType->accrual_rate;

        if ($leaveType->accrual_cap !== null) {
            return (float) min($accrued, $leaveType->accrual_cap);
        }

        return (float) $accrued;
    }
}

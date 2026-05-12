<?php

namespace App\Services;

use App\Models\LeavePolicy;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class PolicyEngineService
{
    public function getActivePolicy(LeaveType $leaveType, Carbon $onDate): ?LeavePolicy
    {
        return LeavePolicy::query()
            ->where('leave_type_id', '=', $leaveType->id)
            ->where('is_active', '=', true)
            ->where('effective_from', '<=', $onDate->toDateString())
            ->where(function ($query) use ($onDate) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $onDate->toDateString());
            })
            ->orderByDesc('version')
            ->first();
    }

    public function validateEligibility(User $user, LeaveType $leaveType, Carbon $start, Carbon $end, float $days): void
    {
        $policy = $this->getActivePolicy($leaveType, $start);

        if ($start->isAfter($end)) {
            throw ValidationException::withMessages([
                'start_date' => ['Start date must be before or equal to end date.'],
            ]);
        }

        $hireDate = $user->hire_date;
        $serviceMonths = $hireDate ? $hireDate->diffInMonths($start) : 0;

        if ($leaveType->code === 'ANNUAL' && $serviceMonths < 11) {
            throw ValidationException::withMessages([
                'leave_type_id' => ['Annual leave is only eligible after 11 months of service.'],
            ]);
        }

        if ($policy && $serviceMonths < $policy->min_service_months) {
            throw ValidationException::withMessages([
                'leave_type_id' => ['Minimum service duration requirement is not met for this leave type.'],
            ]);
        }

        if ($policy?->allow_backdate === false && $start->isBefore(now()->startOfDay())) {
            throw ValidationException::withMessages([
                'start_date' => ['Backdated leave requests are not allowed by policy.'],
            ]);
        }

        if ($policy?->allow_future_apply_days !== null) {
            $daysAhead = now()->startOfDay()->diffInDays($start, false);

            if ($daysAhead > (int) $policy->allow_future_apply_days) {
                throw ValidationException::withMessages([
                    'start_date' => ['Leave request exceeds maximum future application window.'],
                ]);
            }
        }

        if ($policy?->max_days_per_year !== null) {
            $year = (int) $start->format('Y');
            $used = (float) LeaveRequest::query()
                ->where('user_id', '=', $user->id)
                ->where('leave_type_id', '=', $leaveType->id)
                ->whereYear('start_date', '=', $year)
                ->where('status', '=', 'approved')
                ->sum('days');

            if ($used + $days > (float) $policy->max_days_per_year) {
                throw ValidationException::withMessages([
                    'leave_type_id' => ['Requested days exceed yearly policy limit for this leave type.'],
                ]);
            }
        }

        if (!$leaveType->is_paid && $policy?->max_unpaid_days_per_year !== null) {
            $year = (int) $start->format('Y');
            $usedUnpaid = (float) LeaveRequest::query()
                ->where('user_id', '=', $user->id)
                ->where('leave_type_id', '=', $leaveType->id)
                ->whereYear('start_date', '=', $year)
                ->where('status', '=', 'approved')
                ->sum('days');

            if ($usedUnpaid + $days > (float) $policy->max_unpaid_days_per_year) {
                throw ValidationException::withMessages([
                    'leave_type_id' => ['Requested unpaid leave exceeds yearly unpaid leave limit.'],
                ]);
            }
        }
    }
}

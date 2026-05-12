<?php

namespace App\Services;

use App\Models\AccrualPlan;
use App\Models\AccrualPlanLevel;
use App\Models\LeaveAllocation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccrualService
{
    public function __construct(
        private LeaveBalanceService $balanceService,
    ) {}

    public function processAllAccruals(): int
    {
        $processed = 0;
        $plans = AccrualPlan::with('levels')->where('is_active', true)->get();

        foreach ($plans as $plan) {
            $allocations = LeaveAllocation::query()
                ->where('leave_type_id', $plan->leave_type_id)
                ->where('allocation_type', 'accrual')
                ->where(function ($q) {
                    $q->whereNull('next_accrual_date')
                        ->orWhere('next_accrual_date', '<=', now());
                })
                ->get();

            foreach ($allocations as $allocation) {
                if ($this->processAccrual($allocation, $plan)) {
                    $processed++;
                }
            }
        }

        return $processed;
    }

    public function processAccrual(LeaveAllocation $allocation, ?AccrualPlan $plan = null): bool
    {
        $plan ??= $allocation->accrualPlan;

        if (!$plan || !$plan->is_active) {
            return false;
        }

        $applicableLevel = $this->getApplicableLevel($plan, $allocation);

        if (!$applicableLevel) {
            return false;
        }

        return DB::transaction(function () use ($allocation, $plan, $applicableLevel) {
            $amount = $applicableLevel->added_value;
            $now = now();

            $newAllocated = (float) $allocation->allocated_days + (float) $amount;
            $yearlyAccrued = (float) $allocation->yearly_accrued_amount + (float) $amount;

            if ($applicableLevel->cap_accrued_time && $applicableLevel->cap_accrued_time_amount) {
                $newAllocated = min($newAllocated, (float) $applicableLevel->cap_accrued_time_amount);
            }

            if ($applicableLevel->cap_accrued_time_yearly && $applicableLevel->cap_accrued_time_yearly_amount) {
                $yearlyAccrued = min($yearlyAccrued, (float) $applicableLevel->cap_accrued_time_yearly_amount);
                $newAllocated = min($newAllocated, (float) $allocation->allocated_days + $yearlyAccrued);
            }

            $nextDate = $this->computeNextAccrualDate($allocation, $applicableLevel);

            $allocation->update([
                'allocated_days' => $newAllocated,
                'total_allocated_days' => (float) $allocation->total_allocated_days + (float) $amount,
                'yearly_accrued_amount' => $yearlyAccrued,
                'last_accrual_date' => $now,
                'next_accrual_date' => $nextDate,
            ]);

            return true;
        });
    }

    private function getApplicableLevel(AccrualPlan $plan, LeaveAllocation $allocation): ?AccrualPlanLevel
    {
        $levels = $plan->levels;

        if ($levels->isEmpty()) {
            return null;
        }

        if ($plan->transition_mode === 'end_of_accrual') {
            $totalAccrued = (float) $allocation->yearly_accrued_amount;
            $currentLevel = $levels->first();

            foreach ($levels as $level) {
                if ($level->cap_accrued_time_amount && $totalAccrued >= (float) $level->cap_accrued_time_amount) {
                    $currentLevel = $level;
                } else {
                    break;
                }
            }

            return $currentLevel;
        }

        return $levels->first();
    }

    private function computeNextAccrualDate(LeaveAllocation $allocation, AccrualPlanLevel $level): ?Carbon
    {
        $lastDate = $allocation->last_accrual_date ?? $allocation->created_at ?? now();

        return match ($level->frequency) {
            'daily' => $lastDate->copy()->addDay(),
            'weekly' => $lastDate->copy()->addWeek(),
            'biweekly' => $lastDate->copy()->addWeeks(2),
            'monthly' => $lastDate->copy()->addMonth(),
            'bimonthly' => $lastDate->copy()->addMonths(2),
            'quarterly' => $lastDate->copy()->addMonths(3),
            'biyearly' => $lastDate->copy()->addMonths(6),
            'yearly' => $lastDate->copy()->addYear(),
            default => $lastDate->copy()->addMonth(),
        };
    }

    public function computeProjectedAccrual(User $user, int $leaveTypeId, Carbon $asOf = null): float
    {
        $asOf ??= now();

        $allocation = LeaveAllocation::query()
            ->where('user_id', $user->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('allocation_type', 'accrual')
            ->where('year', $asOf->year)
            ->first();

        if (!$allocation || !$allocation->accrualPlan) {
            return 0.0;
        }

        $levels = $allocation->accrualPlan->levels;
        if ($levels->isEmpty()) {
            return 0.0;
        }

        $lastDate = $allocation->last_accrual_date ?? $allocation->created_at?->startOfYear() ?? $asOf->copy()->startOfYear();
        $monthlyRate = $levels->first()->added_value ?? 0;
        $monthsElapsed = max(0, $lastDate->diffInMonths($asOf));

        $projected = $monthsElapsed * (float) $monthlyRate;
        $totalWithExisting = (float) $allocation->total_allocated_days + $projected;

        if ($levels->first()->cap_accrued_time_amount) {
            return min($totalWithExisting, (float) $levels->first()->cap_accrued_time_amount);
        }

        return $totalWithExisting;
    }
}

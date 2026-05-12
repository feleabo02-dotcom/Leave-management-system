<?php

namespace App\Services;

use App\Models\LeaveAllocation;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class LeaveDashboardService
{
    public function __construct(
        private LeaveBalanceService $balanceService,
    ) {}

    public function getYearCalendarData(User $user, int $year = null): Collection
    {
        $year ??= now()->year;

        $requests = LeaveRequest::query()
            ->with('leaveType')
            ->where('user_id', $user->id)
            ->whereYear('start_date', '<=', $year)
            ->whereYear('end_date', '>=', $year)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $colorMap = LeaveType::query()->where('active', true)->pluck('color', 'id')->map(fn($c) => $c ?: '#6366f1');

        $calendarData = [];
        $start = Carbon::create($year, 1, 1);
        $end = Carbon::create($year, 12, 31);

        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            $dayReqs = $requests->filter(fn($r) => $date->between($r->start_date, $r->end_date));

            if ($dayReqs->isNotEmpty()) {
                $statuses = $dayReqs->pluck('status')->unique();
                $typeIds = $dayReqs->pluck('leave_type_id')->unique();
                $color = $colorMap->get($typeIds->first(), '#6366f1');

                $calendarData[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->day,
                    'month' => $date->month,
                    'is_today' => $date->isToday(),
                    'is_weekend' => $date->isWeekend(),
                    'has_leave' => true,
                    'is_pending' => $statuses->diff(['approved', 'rejected'])->isNotEmpty(),
                    'is_rejected' => $statuses->contains('rejected'),
                    'is_approved' => $statuses->every(fn($s) => $s === 'approved'),
                    'requests' => $dayReqs->map(fn($r) => [
                        'id' => $r->id,
                        'type' => $r->leaveType->name,
                        'status' => $r->status,
                        'color' => $colorMap->get($r->leave_type_id, '#6366f1'),
                        'is_half_day' => $r->request_unit === 'half_day',
                    ])->values()->toArray(),
                ];
            } else {
                $calendarData[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->day,
                    'month' => $date->month,
                    'is_today' => $date->isToday(),
                    'is_weekend' => $date->isWeekend(),
                    'has_leave' => false,
                ];
            }
        }

        return collect($calendarData);
    }

    public function getOverviewStats(User $user, int $year = null): array
    {
        $year ??= now()->year;

        $leaveTypes = LeaveType::query()->where('active', true)->get();
        $stats = [];

        foreach ($leaveTypes as $type) {
            $allocation = LeaveAllocation::query()
                ->where('user_id', $user->id)
                ->where('leave_type_id', $type->id)
                ->where('year', $year)
                ->first();

            $used = LeaveRequest::query()
                ->where('user_id', $user->id)
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->sum('days');

            $pending = LeaveRequest::query()
                ->where('user_id', $user->id)
                ->where('leave_type_id', $type->id)
                ->whereIn('status', ['submitted', 'manager_approved'])
                ->whereYear('start_date', $year)
                ->sum('days');

            $allocated = (float) ($allocation?->total_allocated_days ?? $allocation?->allocated_days ?? 0) + (float) ($allocation?->carried_over_days ?? 0);
            $remaining = (float) max(0, $allocated - $used);

            $stats[] = [
                'type' => $type,
                'allocated' => $allocated,
                'used' => (float) $used,
                'pending' => (float) $pending,
                'remaining' => $remaining,
                'allocation' => $allocation,
            ];
        }

        return $stats;
    }

    public function getTeamCalendar(User $manager, int $year = null): Collection
    {
        $year ??= now()->year;

        $teamIds = User::query()
            ->where('manager_id', $manager->id)
            ->where('status', 'active')
            ->pluck('id');

        if ($teamIds->isEmpty()) {
            return collect();
        }

        $requests = LeaveRequest::query()
            ->with(['user', 'leaveType'])
            ->whereIn('user_id', $teamIds)
            ->whereYear('start_date', '<=', $year)
            ->whereYear('end_date', '>=', $year)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->orderBy('start_date')
            ->get()
            ->groupBy('user_id');

        return $requests;
    }
}

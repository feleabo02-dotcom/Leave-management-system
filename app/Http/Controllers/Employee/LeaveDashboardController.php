<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveBalanceService;
use App\Services\LeaveDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveDashboardController extends Controller
{
    public function __construct(
        private LeaveBalanceService $balanceService,
        private LeaveDashboardService $dashboardService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $year = (int) ($request->get('year', now()->year));

        $leaveTypes = LeaveType::query()->where('active', true)->get();
        $balances = collect();

        foreach ($leaveTypes as $type) {
            $allocation = $this->balanceService->getAllocation($user, $type, $year);
            $remaining = $this->balanceService->getRemaining($user, $type, $year);

            $balances->push((object) [
                'type' => $type,
                'remaining' => $remaining,
                'allocated' => ($allocation?->total_allocated_days ?? $allocation?->allocated_days ?? 0) + ($allocation?->carried_over_days ?? 0),
                'used' => $allocation?->used_days ?? 0,
            ]);
        }

        $recentRequests = LeaveRequest::query()
            ->with('leaveType')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $calendarData = $this->dashboardService->getYearCalendarData($user, $year);
        $overviewStats = $this->dashboardService->getOverviewStats($user, $year);

        $years = range(now()->year - 2, now()->year + 2);

        $hoursPerDay = $this->balanceService->getHoursPerDay();

        return view('erp.employee.dashboard', compact(
            'leaveTypes',
            'balances',
            'recentRequests',
            'calendarData',
            'overviewStats',
            'year',
            'years',
            'hoursPerDay',
        ));
    }

    public function calendar(Request $request): View
    {
        $user = $request->user();
        $year = (int) ($request->get('year', now()->year));
        $month = (int) ($request->get('month', now()->month));

        $calendarData = $this->dashboardService->getYearCalendarData($user, $year);
        $overviewStats = $this->dashboardService->getOverviewStats($user, $year);

        $years = range(now()->year - 2, now()->year + 2);
        $months = range(1, 12);

        return view('erp.employee.calendar', compact(
            'calendarData',
            'overviewStats',
            'year',
            'month',
            'years',
            'months',
        ));
    }
}

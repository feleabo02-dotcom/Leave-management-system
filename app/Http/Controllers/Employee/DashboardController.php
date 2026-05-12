<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveBalanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $leaveTypes = LeaveType::query()->where('active', '=', true)->get();
        $balanceService = app(LeaveBalanceService::class);
        $year = now()->year;

        $balances = $leaveTypes->map(function ($type) use ($user, $balanceService, $year) {
            $remaining = $balanceService->getRemaining($user, $type, $year);
            $allocation = $balanceService->getAllocation($user, $type, $year);
            $allocated = $allocation?->allocated_days ?? 0;
            $used = $allocation?->used_days ?? 0;

            if ($type->allocation_type === 'accrual') {
                $allocated = $balanceService->calculateAccruedDays($type);
            }

            return (object) [
                'type' => $type,
                'allocated' => $allocated,
                'used' => $used,
                'remaining' => $remaining,
            ];
        });

        $recentRequests = LeaveRequest::query()
            ->with('leaveType')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $hoursPerDay = $balanceService->getHoursPerDay();

        return view('erp.employee.dashboard', compact('leaveTypes', 'balances', 'recentRequests', 'hoursPerDay'));
    }
}

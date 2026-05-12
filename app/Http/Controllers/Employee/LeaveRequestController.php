<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveBalanceService;
use App\Services\LeaveRequestService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $leaveTypes = LeaveType::query()->where('active', '=', true)->get();
        $requests = LeaveRequest::query()
            ->with('leaveType')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('erp.employee.requests', compact('leaveTypes', 'requests'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $leaveTypes = LeaveType::query()->where('active', '=', true)->get();
        $balanceService = app(LeaveBalanceService::class);
        $year = now()->year;

        $balances = $leaveTypes->map(function ($type) use ($user, $balanceService, $year) {
            $remaining = $balanceService->getRemaining($user, $type, $year);
            $allocation = $balanceService->getAllocation($user, $type, $year);
            $allocated = $allocation?->allocated_days ?? 0;

            if ($type->allocation_type === 'accrual') {
                $allocated = $balanceService->calculateAccruedDays($type);
            }

            return (object) [
                'type' => $type,
                'remaining' => $remaining,
                'allocated' => $allocated,
            ];
        });

        $hoursPerDay = $balanceService->getHoursPerDay();

        return view('erp.employee.leave.create', compact('leaveTypes', 'balances', 'hoursPerDay'));
    }

    public function store(Request $request, LeaveRequestService $service): RedirectResponse
    {
        $data = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],
            'request_unit' => ['nullable', 'in:day,half_day,hour'],
            'requested_hours' => ['nullable', 'numeric', 'min:0.5'],
            'half_day_period' => ['nullable', 'in:am,pm'],
        ]);

        $leaveType = LeaveType::query()->findOrFail($data['leave_type_id']);

        $service->createRequest(
            $request->user(),
            $leaveType,
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
            $data['reason'] ?? null,
            $data['request_unit'] ?? null,
            $data['requested_hours'] ?? null,
            $data['half_day_period'] ?? null
        );

        return redirect()->route('employee.requests')->with('status', 'request-submitted');
    }
}

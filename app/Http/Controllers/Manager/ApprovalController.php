<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveDashboardService;
use App\Services\LeaveRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function __construct(
        private LeaveDashboardService $dashboardService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $pendingRequests = LeaveRequest::query()
            ->with(['user', 'leaveType', 'user.manager'])
            ->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                    ->orWhereHas('user', fn($q) => $q->where('manager_id', $user->id));
            })
            ->whereIn('status', ['submitted', 'manager_approved'])
            ->latest()
            ->get();

        $history = LeaveRequest::query()
            ->with(['user', 'leaveType'])
            ->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                    ->orWhereHas('user', fn($q) => $q->where('manager_id', $user->id));
            })
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->take(20)
            ->get();

        $teamCalendar = $this->dashboardService->getTeamCalendar($user);

        $stats = [
            'pending' => $pendingRequests->count(),
            'approved_today' => LeaveRequest::query()
                ->where('manager_id', $user->id)
                ->where('status', 'approved')
                ->whereDate('approved_at', today())
                ->count(),
            'team_size' => User::query()->where('manager_id', $user->id)->count(),
        ];

        return view('erp.manager.dashboard', compact(
            'pendingRequests',
            'history',
            'teamCalendar',
            'stats',
        ));
    }

    public function approveManager(LeaveRequest $leaveRequest, LeaveRequestService $service): RedirectResponse
    {
        $service->approveManager($leaveRequest, request()->user());

        return back()->with('status', 'manager-approved');
    }

    public function approveHr(LeaveRequest $leaveRequest, LeaveRequestService $service): RedirectResponse
    {
        $service->approveHr($leaveRequest, request()->user());

        return back()->with('status', 'hr-approved');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest, LeaveRequestService $service): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $service->reject($leaveRequest, $request->user(), $data['reason']);

        return back()->with('status', 'request-rejected');
    }
}

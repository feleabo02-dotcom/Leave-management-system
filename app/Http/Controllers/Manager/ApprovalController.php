<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $requests = LeaveRequest::query()
            ->with(['user', 'leaveType'])
            ->where('manager_id', $request->user()->id)
            ->whereIn('status', ['submitted', 'manager_approved'])
            ->latest()
            ->get();

        return view('erp.manager.dashboard', compact('requests'));
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
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $service->reject($leaveRequest, $request->user(), $data['reason']);

        return back()->with('status', 'request-rejected');
    }
}

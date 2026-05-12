<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveAllocation;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function byEmployee(Request $request): View
    {
        $year = (int) ($request->get('year', now()->year));
        $departmentId = $request->get('department_id');

        $employees = User::query()
            ->with(['leaveAllocations' => fn($q) => $q->where('year', $year)->with('leaveType')])
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->where('status', 'active')
            ->get();

        $leaveTypes = LeaveType::query()->where('active', true)->get();

        $reportData = [];

        foreach ($employees as $employee) {
            $row = ['employee' => $employee];

            foreach ($leaveTypes as $type) {
                $alloc = $employee->leaveAllocations->firstWhere('leave_type_id', $type->id);
                $used = LeaveRequest::query()
                    ->where('user_id', $employee->id)
                    ->where('leave_type_id', $type->id)
                    ->where('status', 'approved')
                    ->whereYear('start_date', $year)
                    ->sum('days');

                $row['types'][$type->id] = [
                    'allocated' => (float) ($alloc?->total_allocated_days ?? $alloc?->allocated_days ?? 0) + (float) ($alloc?->carried_over_days ?? 0),
                    'used' => (float) $used,
                    'remaining' => (float) max(0, (($alloc?->total_allocated_days ?? $alloc?->allocated_days ?? 0) + ($alloc?->carried_over_days ?? 0)) - $used),
                ];
            }

            $reportData[] = $row;
        }

        $years = range(now()->year - 2, now()->year + 2);

        return view('erp.manager.reports.by-employee', compact('reportData', 'leaveTypes', 'year', 'years'));
    }

    public function summary(Request $request): View
    {
        $year = (int) ($request->get('year', now()->year));

        $leaveTypes = LeaveType::query()->where('active', true)->get();
        $summary = [];

        foreach ($leaveTypes as $type) {
            $totalAllocated = (float) LeaveAllocation::query()
                ->where('leave_type_id', $type->id)
                ->where('year', $year)
                ->sum('total_allocated_days');

            $totalUsed = (float) LeaveRequest::query()
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->sum('days');

            $totalPending = (float) LeaveRequest::query()
                ->where('leave_type_id', $type->id)
                ->whereIn('status', ['submitted', 'manager_approved'])
                ->whereYear('start_date', $year)
                ->sum('days');

            $employeeCount = LeaveAllocation::query()
                ->where('leave_type_id', $type->id)
                ->where('year', $year)
                ->count();

            $summary[] = [
                'type' => $type,
                'total_allocated' => $totalAllocated,
                'total_used' => $totalUsed,
                'total_pending' => $totalPending,
                'total_remaining' => max(0, $totalAllocated - $totalUsed),
                'employee_count' => $employeeCount,
            ];
        }

        $years = range(now()->year - 2, now()->year + 2);

        return view('erp.manager.reports.summary', compact('summary', 'year', 'years'));
    }

    public function balanceReport(Request $request): View
    {
        $year = (int) ($request->get('year', now()->year));
        $search = $request->get('search');

        $employees = User::query()
            ->with(['leaveAllocations' => fn($q) => $q->where('year', $year)->with('leaveType')])
            ->where('status', 'active')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->get();

        $leaveTypes = LeaveType::query()->where('active', true)->get();

        $years = range(now()->year - 2, now()->year + 2);

        return view('erp.manager.reports.balance', compact('employees', 'leaveTypes', 'year', 'years', 'search'));
    }
}

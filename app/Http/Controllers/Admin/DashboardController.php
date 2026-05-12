<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveAllocation;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeEmployees = Employee::where('status', 'active')->count();
        $totalEmployees = Employee::count();
        $leaveTypes = LeaveType::count();
        $totalAllocations = LeaveAllocation::count();
        $usersWithAllocations = $totalEmployees > 0 ? round(($totalAllocations / max($totalEmployees, 1)) * 100) : 0;
        $auditLogs = AuditLog::count();
        $recentHires = Employee::with(['user', 'department'])
            ->latest()
            ->take(5)
            ->get();

        return view('erp.admin.dashboard', compact(
            'activeEmployees',
            'totalEmployees',
            'leaveTypes',
            'usersWithAllocations',
            'auditLogs',
            'recentHires',
        ));
    }
}

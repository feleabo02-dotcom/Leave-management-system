<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\AuditLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ── Core KPIs ──────────────────────────────────────────────────────
        $stats = [
            'total_employees'    => User::count(),
            'active_employees'   => User::where('status', 'active')->count(),
            'departments'        => Department::count(),
            'pending_leaves'     => LeaveRequest::where('status', 'submitted')->count(),
            'approved_leaves'    => LeaveRequest::where('status', 'approved')
                                        ->whereMonth('created_at', now()->month)->count(),
            'my_pending_leaves'  => LeaveRequest::where('user_id', $user->id)
                                        ->whereIn('status', ['submitted', 'under_review'])->count(),
        ];

        // ── Recent Activity ─────────────────────────────────────────────────
        $recentActivity = AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // ── Pending Approvals for current user ──────────────────────────────
        $pendingApprovals = collect();
        if ($user->hasPermission('leave.approve')) {
            $pendingApprovals = LeaveRequest::with(['user', 'leaveType'])
                ->where('status', 'submitted')
                ->latest()
                ->take(5)
                ->get();
        }

        // ── Leave by department (chart data) ────────────────────────────────
        $leaveByDept = LeaveRequest::join('users', 'leave_requests.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->where('leave_requests.status', 'approved')
            ->whereYear('leave_requests.created_at', now()->year)
            ->select('departments.name as dept', DB::raw('COUNT(*) as total'))
            ->groupBy('departments.name')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // ── Unread notifications ─────────────────────────────────────────────
        $unreadCount = NotificationService::unreadCount($user->id);

        return view('dashboard', compact(
            'stats',
            'recentActivity',
            'pendingApprovals',
            'leaveByDept',
            'unreadCount',
        ));
    }
}

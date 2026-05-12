<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\Task;
use App\Helpers\DatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $manager = auth()->user();
        $reportIds = $manager->directReports()->pluck('id');
        $employeeIds = \App\Models\Employee::whereIn('user_id', $reportIds)->pluck('id');

        // 1. Team Summary
        $teamCount = $reportIds->count();
        $pendingApprovals = LeaveRequest::whereIn('user_id', $reportIds)
            ->where('status', 'pending')
            ->count();
        
        $teamAttendanceToday = Attendance::whereIn('employee_id', $employeeIds)
            ->whereDate('check_in', today())
            ->count();

        // 2. Team Capacity Chart (Doughnut)
        $onLeaveToday = LeaveRequest::whereIn('user_id', $reportIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();
        
        $availableToday = $teamCount - $onLeaveToday;

        // 3. Team Productivity (Weekly Tasks - Bar Chart)
        $tasksCompleted = Task::whereIn('assigned_to', $reportIds)
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->startOfWeek())
            ->selectRaw('COUNT(*) as count, ' . DatabaseHelper::dayOfWeek('updated_at'))
            ->groupBy('day')
            ->get()
            ->pluck('count', 'day');
        
        $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $chartTasks = [];
        for ($i = 1; $i <= 7; $i++) {
            $dayIndex = $i % 7;
            $chartTasks[] = $tasksCompleted[$dayIndex] ?? 0;
        }

        // 4. Recent Team Activity
        $recentLeaves = LeaveRequest::with('user', 'leaveType')
            ->whereIn('user_id', $reportIds)
            ->latest()
            ->take(5)
            ->get();

        return view('erp.manager.dashboard', compact(
            'teamCount',
            'pendingApprovals',
            'teamAttendanceToday',
            'onLeaveToday',
            'availableToday',
            'chartTasks',
            'weekDays',
            'recentLeaves'
        ));
    }
}

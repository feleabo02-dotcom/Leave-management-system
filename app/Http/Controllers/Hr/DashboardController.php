<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Models\Attendance;
use App\Helpers\DatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $probationEmployees = Employee::where('status', 'probation')->count();
        $terminatedEmployees = Employee::where('status', 'terminated')->count();

        $newHires = Employee::whereBetween('hire_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $newHiresThisYear = Employee::whereYear('hire_date', now()->year)->count();

        // 1. Department Distribution (Doughnut/Circle Bar)
        $departmentCounts = Employee::select('department_id', DB::raw('count(*) as total'))
            ->where('status', '!=', 'terminated')
            ->groupBy('department_id')
            ->with('department')
            ->get();

        // 2. Hiring Trend (Last 6 Months - Area Chart)
        $hiringTrend = Employee::where('hire_date', '>=', now()->subMonths(6))
            ->selectRaw('COUNT(*) as count, ' . DatabaseHelper::month('hire_date'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [];
        $hiringCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('m');
            $months[] = now()->subMonths($i)->format('M');
            $hiringCounts[] = $hiringTrend->firstWhere('month', $month)->count ?? 0;
        }

        // 3. Attendance Status Today (Pie)
        $todayAttendance = [
            'present' => Attendance::whereDate('check_in', today())->count(),
            'absent' => $activeEmployees - Attendance::whereDate('check_in', today())->count()
        ];

        $recentEmployees = Employee::with(['user', 'department', 'position'])
            ->latest()
            ->take(5)
            ->get();

        return view('hr.dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'probationEmployees',
            'terminatedEmployees',
            'newHires',
            'newHiresThisYear',
            'departmentCounts',
            'recentEmployees',
            'months',
            'hiringCounts',
            'todayAttendance'
        ));
    }
}

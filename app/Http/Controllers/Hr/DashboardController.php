<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
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

        $departmentCounts = Employee::select('department_id', DB::raw('count(*) as total'))
            ->where('status', '!=', 'terminated')
            ->groupBy('department_id')
            ->with('department')
            ->get();

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
        ));
    }
}

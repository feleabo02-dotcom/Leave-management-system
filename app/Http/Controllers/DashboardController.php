<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. High Priority Roles
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return (new \App\Http\Controllers\Admin\DashboardController())->index();
        }

        if ($user->hasRole('hr_manager')) {
            return (new \App\Http\Controllers\Hr\DashboardController())->index();
        }

        // 2. Manager Role
        if ($user->hasRole('manager')) {
            return (new \App\Http\Controllers\Manager\DashboardController())->index();
        }

        // 3. Employee Fallback
        if ($user->hasRole('employee')) {
            return (new \App\Http\Controllers\Employee\LeaveDashboardController(
                app(\App\Services\LeaveBalanceService::class),
                app(\App\Services\LeaveDashboardService::class)
            ))->index($request);
        }

        // 4. Final Fallback (Security)
        abort(403, 'Unauthorized dashboard access. Please contact your administrator.');
    }
}

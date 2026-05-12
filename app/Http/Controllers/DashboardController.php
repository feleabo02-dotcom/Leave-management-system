<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('hr_manager')) {
            return redirect()->route('hr.dashboard');
        }

        if ($user->hasRole('manager')) {
            return redirect()->route('manager.dashboard');
        }

        return redirect()->route('employee.dashboard');
    }
}

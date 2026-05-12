<?php

namespace App\Providers;

use App\Models\Employee;
use App\Observers\EmployeeObserver;
use App\Models\LeaveRequest;
use App\Policies\LeaveRequestPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Employee::observe(EmployeeObserver::class);
        Gate::policy(LeaveRequest::class, LeaveRequestPolicy::class);

        // ── Super Admin: bypass all gates ─────────────────────────────────
        Gate::before(function (?User $user) {
            if ($user && $user->hasRole('super_admin')) {
                return true;
            }
        });

        // ── Module-level Gates ────────────────────────────────────────────
        $modules = [
            'employees', 'attendance', 'leave', 'payroll', 'assets',
            'inventory', 'procurement', 'crm', 'sales', 'accounting',
            'manufacturing', 'projects', 'helpdesk', 'reports',
            'companies', 'settings', 'users', 'roles',
        ];

        $actions = ['create', 'read', 'update', 'delete', 'approve', 'export'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $slug = "$module.$action";
                Gate::define($slug, function (User $user) use ($slug) {
                    return $user->hasPermission($slug);
                });
            }
        }
    }
}

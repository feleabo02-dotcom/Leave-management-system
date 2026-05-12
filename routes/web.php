<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Dashboard (role-aware) ────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Notifications ─────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('/{id}/read',     [NotificationController::class, 'markRead'])->name('mark-read');
    });

    // ── Companies & Branches (Phase 1) ────────────────────────────────────
    Route::resource('companies', CompanyController::class);
    Route::resource('companies.branches', BranchController::class)->shallow()->only(['store', 'update', 'destroy']);

    // ── Profile ────────────────────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Phase 2 — HR & Employees ────────────────────────────────────────────
    Route::get('employees/org-chart', [\App\Http\Controllers\EmployeeController::class, 'orgChart'])->name('employees.org-chart');
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
    Route::post('employees/{employee}/contracts', [\App\Http\Controllers\ContractController::class, 'store'])->name('employees.contracts.store');
    Route::delete('employees/{employee}/contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'destroy'])->name('employees.contracts.destroy');
    Route::post('employees/{employee}/documents', [\App\Http\Controllers\EmployeeDocumentController::class, 'store'])->name('employees.documents.store');
    Route::delete('employees/{employee}/documents/{document}', [\App\Http\Controllers\EmployeeDocumentController::class, 'destroy'])->name('employees.documents.destroy');

    // ── Positions ───────────────────────────────────────────────────────────
    Route::resource('positions', \App\Http\Controllers\PositionController::class);

    // ── Employee Leave ──────────────────────────────────────────────────────
    Route::get('/leave-requests', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/leave-requests/create', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/leave-requests', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'store'])->name('employee.requests.store');
    Route::get('/leave-requests/preview', [\App\Http\Controllers\Employee\LeavePreviewController::class, '__invoke'])->name('employee.requests.preview');

    // ── Employee Leave Dashboard (Odoo-style) ────────────────────────────────
    Route::get('/employee/leave-dashboard', [\App\Http\Controllers\Employee\LeaveDashboardController::class, 'index'])->name('employee.dashboard');

    // ── Manager Approval ────────────────────────────────────────────────────
    Route::get('/manager', [\App\Http\Controllers\Manager\ApprovalController::class, 'index'])->name('manager.dashboard');
    Route::post('/manager/approvals/{leaveRequest}/approve-manager', [\App\Http\Controllers\Manager\ApprovalController::class, 'approveManager'])->name('manager.approvals.manager');
    Route::post('/manager/approvals/{leaveRequest}/approve-hr', [\App\Http\Controllers\Manager\ApprovalController::class, 'approveHr'])->name('manager.approvals.hr');
    Route::post('/manager/approvals/{leaveRequest}/reject', [\App\Http\Controllers\Manager\ApprovalController::class, 'reject'])->name('manager.approvals.reject');

    // ── Manager Reports ──────────────────────────────────────────────────────
    Route::prefix('manager/reports')->name('manager.reports.')->group(function () {
        Route::get('/by-employee', [\App\Http\Controllers\Manager\ReportController::class, 'byEmployee'])->name('by-employee');
        Route::get('/summary', [\App\Http\Controllers\Manager\ReportController::class, 'summary'])->name('summary');
        Route::get('/balance', [\App\Http\Controllers\Manager\ReportController::class, 'balanceReport'])->name('balance');
    });

    // ── Manager Sidebar ─────────────────────────────────────────────────────
    Route::get('/manager/team', fn() => view('erp.manager.team'))->name('manager.team');
    Route::get('/manager/calendar', fn() => view('erp.manager.calendar'))->name('manager.calendar');

    // ── Employee Sidebar ────────────────────────────────────────────────────
    Route::get('/employee/requests', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'index'])->name('employee.requests');
    Route::get('/employee/calendar', [\App\Http\Controllers\Employee\LeaveDashboardController::class, 'calendar'])->name('employee.calendar');
    Route::get('/employee/notifications', fn() => view('erp.employee.notifications'))->name('employee.notifications');

    // ── Admin / HR Management ──────────────────────────────────────────────
    Route::get('/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/leave-types', [\App\Http\Controllers\Admin\LeaveTypeController::class, 'index'])->name('admin.leave-types');
    Route::post('/admin/leave-types', [\App\Http\Controllers\Admin\LeaveTypeController::class, 'store'])->name('admin.leave-types.store');
    Route::get('/admin/leave-policies', [\App\Http\Controllers\Admin\LeavePolicyController::class, 'index'])->name('admin.leave-policies');
    Route::post('/admin/leave-policies', [\App\Http\Controllers\Admin\LeavePolicyController::class, 'store'])->name('admin.leave-policies.store');
    Route::post('/admin/leave-policies/{leavePolicy}/activate', [\App\Http\Controllers\Admin\LeavePolicyController::class, 'activate'])->name('admin.leave-policies.activate');
    Route::get('/admin/allocations', [\App\Http\Controllers\Admin\AllocationController::class, 'index'])->name('admin.allocations');
    Route::post('/admin/allocations', [\App\Http\Controllers\Admin\AllocationController::class, 'store'])->name('admin.allocations.store');
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{user}/assign-role', [\App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('admin.users.assign-role');
    Route::get('/admin/settings', fn() => view('erp.admin.settings'))->name('admin.settings');

    // ── Admin Accrual Plans ──────────────────────────────────────────────────
    Route::get('/admin/accrual-plans', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'index'])->name('admin.accrual-plans');
    Route::post('/admin/accrual-plans', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'store'])->name('admin.accrual-plans.store');
    Route::delete('/admin/accrual-plans/{accrualPlan}', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'destroy'])->name('admin.accrual-plans.destroy');
    Route::post('/admin/accrual-plans/{accrualPlan}/levels', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'storeLevel'])->name('admin.accrual-levels.store');
    Route::delete('/admin/accrual-plans/{accrualPlan}/levels/{level}', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'destroyLevel'])->name('admin.accrual-levels.destroy');
    Route::post('/admin/accrual-plans/run', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'runAccruals'])->name('admin.accrual-plans.run');

    // ── HR Dashboard ────────────────────────────────────────────────────────
    Route::get('/hr/dashboard', [\App\Http\Controllers\Hr\DashboardController::class, 'index'])->name('hr.dashboard');

    // ── Placeholder routes for future phases ────────────────────────────────
    // ── Phase 3 — Attendance ──────────────────────────────────────────────
    Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/my-attendance', [\App\Http\Controllers\AttendanceController::class, 'myAttendance'])->name('attendance.my');
    Route::post('/attendance/check-in', [\App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [\App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    // ── Phase 4 — Payroll ────────────────────────────────────────────────
    Route::get('/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/my-payslips', [\App\Http\Controllers\PayrollController::class, 'myPayslips'])->name('payroll.my');
    Route::get('/payroll/{payroll}', [\App\Http\Controllers\PayrollController::class, 'show'])->name('payroll.show');
    Route::post('/payroll/generate', [\App\Http\Controllers\PayrollController::class, 'generate'])->name('payroll.generate');
    // ── Phase 5 — Asset Management ───────────────────────────────────────
    Route::resource('assets', \App\Http\Controllers\AssetController::class);
    Route::get('/my-assets', [\App\Http\Controllers\AssetController::class, 'myAssets'])->name('assets.my');
    Route::post('/assets/{asset}/assign', [\App\Http\Controllers\AssetController::class, 'assign'])->name('assets.assign');
    Route::post('/assets/{asset}/return', [\App\Http\Controllers\AssetController::class, 'return'])->name('assets.return');
    // ── Phase 6 — Inventory Management ───────────────────────────────────
    Route::resource('inventory', \App\Http\Controllers\InventoryController::class);
    Route::post('/inventory/adjust', [\App\Http\Controllers\InventoryController::class, 'adjustStock'])->name('inventory.adjust');
    // ── Phase 7 — Procurement ────────────────────────────────────────────
    Route::resource('procurement', \App\Http\Controllers\ProcurementController::class);
    Route::post('/procurement/{order}/receive', [\App\Http\Controllers\ProcurementController::class, 'receive'])->name('procurement.receive');
    // ── Phase 8 — CRM ───────────────────────────────────────────────────
    Route::get('/crm', [\App\Http\Controllers\CrmController::class, 'index'])->name('crm.index');
    Route::get('/crm/pipeline', [\App\Http\Controllers\CrmController::class, 'pipeline'])->name('crm.pipeline');
    Route::post('/crm/customers', [\App\Http\Controllers\CrmController::class, 'storeCustomer'])->name('crm.customers.store');
    Route::post('/crm/opportunities', [\App\Http\Controllers\CrmController::class, 'storeOpportunity'])->name('crm.opportunities.store');
    Route::get('/crm/opportunities/{opportunity}', [\App\Http\Controllers\CrmController::class, 'showOpportunity'])->name('crm.opportunities.show');
    Route::post('/crm/opportunities/{opportunity}/stage', [\App\Http\Controllers\CrmController::class, 'updateStage'])->name('crm.opportunities.stage');
    Route::post('/crm/opportunities/{opportunity}/activity', [\App\Http\Controllers\CrmController::class, 'logActivity'])->name('crm.opportunities.activity');
    Route::get('/sales',                 fn() => abort(503, 'Coming in Phase 9'))->name('sales.index');
    Route::get('/accounting',            fn() => abort(503, 'Coming in Phase 10'))->name('accounting.index');
    Route::get('/manufacturing',         fn() => abort(503, 'Coming in Phase 11'))->name('manufacturing.index');
    Route::get('/projects',              fn() => abort(503, 'Coming in Phase 12'))->name('projects.index');
    Route::get('/helpdesk',              fn() => abort(503, 'Coming in Phase 12'))->name('helpdesk.index');
    Route::get('/reports',               fn() => abort(503, 'Coming in Phase 13'))->name('reports.index');
});

require __DIR__ . '/auth.php';

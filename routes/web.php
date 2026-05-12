<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — ERP System
|--------------------------------------------------------------------------
|
| Phase 1 routes are active. Subsequent module routes will be added
| incrementally as each phase is built out.
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ── Authenticated Routes ───────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────
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

    /*
    |----------------------------------------------------------------------
    | Placeholder routes — will be replaced as each phase is implemented.
    | Defined here so the sidebar can link to them without 404 errors.
    |----------------------------------------------------------------------
    */

    // Phase 2 — HR & Employees
    Route::get('/employees',             fn() => abort(503, 'Coming in Phase 2'))->name('employees.index');
    Route::get('/employees/create',      fn() => abort(503, 'Coming in Phase 2'))->name('employees.create');

    // Phase 3 — Attendance
    Route::get('/attendance',            fn() => abort(503, 'Coming in Phase 3'))->name('attendance.index');

    // Leave (existing system — keep working)
    Route::get('/leave-requests',        fn() => abort(503, 'Coming soon'))->name('leave-requests.index');
    Route::get('/leave-requests/create', fn() => abort(503, 'Coming soon'))->name('leave-requests.create');
    Route::get('/leave-requests/{id}',   fn() => abort(503, 'Coming soon'))->name('leave-requests.show');

    // Phase 4 — Payroll
    Route::get('/payroll',               fn() => abort(503, 'Coming in Phase 4'))->name('payroll.index');

    // Phase 5 — Assets
    Route::get('/assets',                fn() => abort(503, 'Coming in Phase 5'))->name('assets.index');
    Route::get('/assets/create',         fn() => abort(503, 'Coming in Phase 5'))->name('assets.create');

    // Phase 6 — Inventory
    Route::get('/inventory',             fn() => abort(503, 'Coming in Phase 6'))->name('inventory.index');

    // Phase 7 — Procurement
    Route::get('/procurement',           fn() => abort(503, 'Coming in Phase 7'))->name('procurement.index');
    Route::get('/procurement/create',    fn() => abort(503, 'Coming in Phase 7'))->name('procurement.create');

    // Phase 8 — CRM
    Route::get('/crm',                   fn() => abort(503, 'Coming in Phase 8'))->name('crm.index');

    // Phase 9 — Sales
    Route::get('/sales',                 fn() => abort(503, 'Coming in Phase 9'))->name('sales.index');

    // Phase 10 — Accounting
    Route::get('/accounting',            fn() => abort(503, 'Coming in Phase 10'))->name('accounting.index');

    // Phase 11 — Manufacturing
    Route::get('/manufacturing',         fn() => abort(503, 'Coming in Phase 11'))->name('manufacturing.index');

    // Phase 12 — Projects & Helpdesk
    Route::get('/projects',              fn() => abort(503, 'Coming in Phase 12'))->name('projects.index');
    Route::get('/helpdesk',              fn() => abort(503, 'Coming in Phase 12'))->name('helpdesk.index');

    // Phase 13 — Reports
    Route::get('/reports',               fn() => abort(503, 'Coming in Phase 13'))->name('reports.index');
});

require __DIR__ . '/auth.php';

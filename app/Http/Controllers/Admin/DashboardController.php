<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SalesOrder;
use App\Models\Account;
use App\Models\Project;
use App\Models\Task;
use App\Models\StockLevel;
use App\Models\JournalItem;
use App\Models\Attendance;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Summary Metrics
        $currentAttendance = Attendance::whereDate('check_in', today())->count();
        $activeTasks = Task::whereIn('status', ['pending', 'in_progress'])->count();
        $monthlyRevenue = SalesOrder::where('status', 'sale')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total_amount');
        $inventoryAlerts = StockLevel::where('quantity', '<', 10)->count();

        // 2. Chart Data: Sales vs Expenses (Bar)
        $salesData = SalesOrder::where('status', 'sale')
            ->where('date', '>=', now()->subMonths(6))
            ->selectRaw('SUM(total_amount) as total, strftime("%m", date) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        $expenseData = JournalItem::whereHas('account', fn($q) => $q->where('type', 'expense'))
            ->whereDate('created_at', '>=', now()->subMonths(6))
            ->selectRaw('SUM(debit) as total, strftime("%m", created_at) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        $months = [];
        $chartSales = [];
        $chartExpenses = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('m');
            $months[] = now()->subMonths($i)->format('M');
            $chartSales[] = $salesData[$month] ?? 0;
            $chartExpenses[] = $expenseData[$month] ?? 0;
        }

        // 3. Revenue Distribution by Product Category
        $revenueByCategory = collect();
        if (Schema::hasTable('product_categories')) {
            $revenueByCategory = DB::table('sales_order_lines')
                ->join('products', 'sales_order_lines.product_id', '=', 'products.id')
                ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
                ->select('product_categories.name', DB::raw('SUM(sales_order_lines.subtotal) as total'))
                ->groupBy('product_categories.name')
                ->get();
        }

        // 4. New Chart Data: Project Status Distribution (Radial/Horizontal Bar)
        $projectStatusCounts = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // 5. Live Attendance Table
        $lastCheckIns = Attendance::with('employee.user')
            ->latest('check_in')
            ->take(5)
            ->get()
            ->map(function($a) {
                $checkInTime = Carbon::parse($a->check_in);
                $isLate = $checkInTime->format('H:i') > '09:00';
                $a->status_badge = $isLate ? 'Late' : 'On Time';
                $a->status_color = $isLate ? 'yellow' : 'green';
                return $a;
            });

        // 6. Recent Activities
        $recentActivities = \App\Models\AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('erp.admin.dashboard', compact(
            'currentAttendance',
            'activeTasks',
            'monthlyRevenue',
            'inventoryAlerts',
            'months',
            'chartSales',
            'chartExpenses',
            'revenueByCategory',
            'projectStatusCounts',
            'lastCheckIns',
            'recentActivities'
        ));
    }
}

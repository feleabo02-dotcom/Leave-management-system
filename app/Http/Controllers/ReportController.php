<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\Attendance;
use App\Models\Account;
use App\Models\JournalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $this->authorize('reports.read');
        return view('erp.reports.index');
    }

    public function payrollSummary()
    {
        $this->authorize('reports.read');
        $payrollData = Payslip::selectRaw("strftime('%m', created_at) as month, strftime('%Y', created_at) as year, SUM(gross_pay) as total_gross, SUM(total_deductions) as total_deductions, SUM(net_pay) as total_net, COUNT(DISTINCT employee_id) as employee_count")
            ->where('status', 'paid')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        return view('erp.reports.payroll-summary', compact('payrollData'));
    }

    public function attendanceSummary()
    {
        $this->authorize('reports.read');
        $attendanceData = Attendance::selectRaw("strftime('%m', check_in) as month, strftime('%Y', check_in) as year, COUNT(*) as total_present, SUM(CASE WHEN strftime('%H:%M', check_in) > '09:00' THEN 1 ELSE 0 END) as total_late")
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        return view('erp.reports.attendance-summary', compact('attendanceData'));
    }

    public function accountingReports(Request $request)
    {
        $this->authorize('reports.read');
        $type = $request->input('type', 'trial_balance');

        $accounts = Account::all();
        $incomeAccounts = Account::where('type', 'income')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();
        $assetAccounts = Account::where('type', 'asset')->get();
        $liabilityAccounts = Account::where('type', 'liability')->get();
        $equityAccounts = Account::where('type', 'equity')->get();
        $netIncome = $incomeAccounts->sum('balance') - $expenseAccounts->sum('balance');

        return view('erp.reports.accounting', compact(
            'type', 'accounts', 'incomeAccounts', 'expenseAccounts',
            'assetAccounts', 'liabilityAccounts', 'equityAccounts', 'netIncome'
        ));
    }
}

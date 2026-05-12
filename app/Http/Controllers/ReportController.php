<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\Attendance;
use App\Models\Account;
use App\Models\JournalItem;
use App\Helpers\DatabaseHelper;
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
        $payrollData = Payslip::selectRaw('SUM(gross_pay) as total_gross, SUM(total_deductions) as total_deductions, SUM(net_pay) as total_net, COUNT(DISTINCT employee_id) as employee_count, ' . DatabaseHelper::month('created_at') . ', ' . DatabaseHelper::year('created_at'))
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

        $records = Attendance::select(
            DB::raw(DatabaseHelper::yearMonth('date')),
            DB::raw('COUNT(*) as total_records'),
            DB::raw("SUM(CASE WHEN status IN ('present','late') THEN 1 ELSE 0 END) as total_present"),
            DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as total_absent"),
            DB::raw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as total_late"),
            DB::raw('SUM(total_hours) as total_hours'),
            DB::raw('SUM(overtime_hours) as total_overtime'),
        )
            ->whereNotNull('date')
            ->groupBy('year_month')
            ->orderBy('year_month', 'desc')
            ->get();

        $attendanceData = $records->map(function ($r) {
            $parts = explode('-', $r->year_month);
            return (object) [
                'year' => $parts[0],
                'month' => $parts[1],
                'total_present' => $r->total_present,
                'total_absent' => $r->total_absent,
                'total_late' => $r->total_late,
                'total_overtime_hours' => $r->total_overtime,
            ];
        });

        return view('erp.reports.attendance-summary', compact('attendanceData'));
    }
}

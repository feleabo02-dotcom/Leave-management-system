<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\SalaryComponent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $this->authorize('payroll.read');
        $payslips = Payslip::with(['employee.user', 'employee.department'])->latest()->paginate(20);
        return view('erp.payroll.index', compact('payslips'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'employee_ids' => 'required|array',
        ]);

        $count = 0;
        foreach ($request->employee_ids as $empId) {
            $employee = Employee::find($empId);
            if (!$employee || !$employee->salary_structure_id) continue;

            $exists = Payslip::where('employee_id', $empId)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->exists();

            if ($exists) continue;

            $this->createPayslip($employee, $request->month, $request->year);
            $count++;
        }

        return back()->with('success', "Generated $count payslips successfully.");
    }

    private function createPayslip(Employee $employee, $month, $year)
    {
        $structure = $employee->salaryStructure;
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $basic = $structure->base_salary;
        $totalAllowance = 0;
        $totalDeduction = 0;
        $lines = [];

        foreach ($structure->components as $comp) {
            $amount = $comp->amount_type === 'percentage' 
                ? ($basic * ($comp->amount / 100)) 
                : $comp->amount;

            if ($comp->type === 'allowance') {
                $totalAllowance += $amount;
            } else {
                $totalDeduction += $amount;
            }

            $lines[] = [
                'name' => $comp->name,
                'code' => $comp->code,
                'type' => $comp->type,
                'amount' => $amount,
            ];
        }

        $net = $basic + $totalAllowance - $totalDeduction;

        $payslip = Payslip::create([
            'employee_id' => $employee->id,
            'month' => $month,
            'year' => $year,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'basic_salary' => $basic,
            'total_allowance' => $totalAllowance,
            'total_deduction' => $totalDeduction,
            'net_salary' => $net,
            'status' => 'draft',
        ]);

        foreach ($lines as $line) {
            $payslip->lines()->create($line);
        }

        return $payslip;
    }

    public function show(Payslip $payroll) // Note: route is payroll.show
    {
        $payslip = $payroll->load(['employee.user', 'employee.department', 'employee.position', 'lines']);
        return view('erp.payroll.show', compact('payslip'));
    }

    public function myPayslips()
    {
        $employee = auth()->user()->employee;
        if (!$employee) return redirect()->route('dashboard')->with('error', 'Employee profile not found.');

        $payslips = Payslip::where('employee_id', $employee->id)
            ->where('status', '!=', 'draft')
            ->latest()
            ->get();

        return view('erp.payroll.my-payslips', compact('payslips'));
    }
}

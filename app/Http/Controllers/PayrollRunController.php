<?php

namespace App\Http\Controllers;

use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollRunController extends Controller
{
    public function index()
    {
        $this->authorize('payroll.read');
        $runs = PayrollRun::with('creator')->latest()->get();
        return view('erp.payroll.runs', compact('runs'));
    }

    public function store(Request $request)
    {
        $this->authorize('payroll.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        PayrollRun::create([
            'name' => $validated['name'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payroll run created successfully.');
    }

    public function generatePayslips(PayrollRun $run)
    {
        $this->authorize('payroll.create');
        $employees = Employee::where('status', 'active')->get();

        foreach ($employees as $employee) {
            Payslip::create([
                'payroll_run_id' => $run->id,
                'employee_id' => $employee->id,
                'status' => 'draft',
            ]);
        }

        return back()->with('success', 'Payslips generated successfully.');
    }

    public function approve(PayrollRun $run)
    {
        $this->authorize('payroll.update');
        $run->update(['status' => 'approved']);
        return back()->with('success', 'Payroll run approved successfully.');
    }

    public function post(PayrollRun $run)
    {
        $this->authorize('payroll.update');
        $run->payslips()->update(['status' => 'paid']);
        $run->update(['status' => 'paid']);
        return back()->with('success', 'Payroll run posted successfully.');
    }
}

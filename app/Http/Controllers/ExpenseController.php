<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $this->authorize('expenses.read');
        $expenses = Expense::with(['employee.user', 'category'])->latest()->paginate(20);
        $categories = ExpenseCategory::all();
        $employees = Employee::with('user')->where('status', 'active')->get();

        return view('erp.expenses.index', compact('expenses', 'categories', 'employees'));
    }

    public function myExpenses()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee profile not found.');
        }

        $expenses = Expense::with('category')->where('employee_id', $employee->id)->latest()->get();

        return view('erp.expenses.my', compact('expenses'));
    }

    public function store(Request $request)
    {
        $this->authorize('expenses.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validated['state'] = 'draft';
        Expense::create($validated);

        return back()->with('success', 'Expense created successfully.');
    }

    public function show(Expense $expense)
    {
        $this->authorize('expenses.read');
        $expense->load(['employee.user', 'category', 'approvedBy', 'department']);

        return view('erp.expenses.show', compact('expense'));
    }

    public function submit(Expense $expense)
    {
        $this->authorize('expenses.update');
        $expense->update(['state' => 'submitted']);

        return back()->with('success', 'Expense submitted successfully.');
    }

    public function approve(Expense $expense)
    {
        $this->authorize('expenses.approve');
        $expense->update([
            'state' => 'approved',
            'approved_by' => auth()->id(),
            'approval_date' => now(),
        ]);

        return back()->with('success', 'Expense approved successfully.');
    }

    public function reject(Expense $expense)
    {
        $this->authorize('expenses.approve');
        $expense->update(['state' => 'rejected']);

        return back()->with('success', 'Expense rejected successfully.');
    }
}

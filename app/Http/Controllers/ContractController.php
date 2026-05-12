<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary' => 'nullable|numeric|min:0',
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        $filePath = null;
        if ($request->hasFile('contract_file')) {
            $filePath = $request->file('contract_file')->store('contracts', 'public');
        }

        // Deactivate previous active contracts
        $employee->contracts()->update(['is_active' => false]);

        $employee->contracts()->create([
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'salary' => $validated['salary'],
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        return back()->with('success', 'Contract added successfully.');
    }

    public function destroy(Employee $employee, Contract $contract)
    {
        if ($contract->file_path) {
            Storage::disk('public')->delete($contract->file_path);
        }
        $contract->delete();

        return back()->with('success', 'Contract removed.');
    }
}

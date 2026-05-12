<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'expiry_date' => 'nullable|date',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $filePath = $request->file('document_file')->store('employee-documents', 'public');

        $employee->documents()->create([
            'type' => $validated['type'],
            'expiry_date' => $validated['expiry_date'],
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(Employee $employee, EmployeeDocument $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return back()->with('success', 'Document removed.');
    }
}

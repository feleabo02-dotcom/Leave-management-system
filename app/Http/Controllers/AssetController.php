<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetHistory;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $this->authorize('assets.read');
        $assets = Asset::with(['category', 'employee.user'])->latest()->paginate(20);
        $categories = AssetCategory::all();
        $employees = Employee::with('user')->where('status', 'active')->get();
        
        return view('erp.assets.index', compact('assets', 'categories', 'employees'));
    }

    public function store(Request $request)
    {
        $this->authorize('assets.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:assets,code',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'location' => 'nullable|string',
        ]);

        Asset::create($validated);

        return back()->with('success', 'Asset registered successfully.');
    }

    public function assign(Request $request, Asset $asset)
    {
        $this->authorize('assets.update');
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        $asset->update([
            'employee_id' => $request->employee_id,
            'status' => 'assigned',
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'employee_id' => $request->employee_id,
            'action' => 'assigned',
            'notes' => $request->notes,
            'action_date' => now(),
        ]);

        return back()->with('success', 'Asset assigned successfully.');
    }

    public function return(Request $request, Asset $asset)
    {
        $this->authorize('assets.update');
        
        $oldEmployeeId = $asset->employee_id;

        $asset->update([
            'employee_id' => null,
            'status' => 'available',
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'employee_id' => $oldEmployeeId,
            'action' => 'returned',
            'notes' => $request->notes,
            'action_date' => now(),
        ]);

        return back()->with('success', 'Asset returned successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'employee.user', 'histories.employee.user']);
        return view('erp.assets.show', compact('asset'));
    }

    public function myAssets()
    {
        $employee = auth()->user()->employee;
        if (!$employee) return redirect()->route('dashboard')->with('error', 'Employee profile not found.');

        $assets = Asset::with('category')->where('employee_id', $employee->id)->get();
        return view('erp.assets.my-assets', compact('assets'));
    }
}

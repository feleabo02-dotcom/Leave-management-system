<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\AssetMaintenance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetEnhanceController extends Controller
{
    public function depreciation(Asset $asset)
    {
        $this->authorize('assets.read');
        $asset->load('depreciation');
        $depreciationSchedule = $asset->depreciation;
        return view('erp.assets.depreciation', compact('asset', 'depreciationSchedule'));
    }

    public function storeDepreciation(Request $request, Asset $asset)
    {
        $this->authorize('assets.create');
        $validated = $request->validate([
            'method' => 'required|string|max:50',
            'original_cost' => 'required|numeric',
            'salvage_value' => 'required|numeric',
            'useful_life_years' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        AssetDepreciation::create([
            'asset_id' => $asset->id,
            'method' => $validated['method'],
            'original_cost' => $validated['original_cost'],
            'salvage_value' => $validated['salvage_value'],
            'useful_life_years' => $validated['useful_life_years'],
            'start_date' => $validated['start_date'],
        ]);

        return back()->with('success', 'Depreciation recorded successfully.');
    }

    public function maintenance()
    {
        $this->authorize('assets.read');
        $maintenances = AssetMaintenance::with(['asset', 'assignee'])->latest()->get();
        $assets = Asset::all();
        $employees = Employee::with('user')->where('status', 'active')->get();
        return view('erp.assets.maintenance', compact('maintenances', 'assets', 'employees'));
    }

    public function storeMaintenance(Request $request)
    {
        $this->authorize('assets.create');
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'scheduled_date' => 'required|date',
            'cost' => 'nullable|numeric',
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        AssetMaintenance::create($validated);

        return back()->with('success', 'Maintenance record created successfully.');
    }

    public function completeMaintenance(AssetMaintenance $maintenance)
    {
        $this->authorize('assets.update');
        $maintenance->update([
            'status' => 'done',
            'completed_date' => now(),
        ]);
        return back()->with('success', 'Maintenance marked as completed.');
    }
}

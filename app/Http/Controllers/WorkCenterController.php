<?php

namespace App\Http\Controllers;

use App\Models\WorkCenter;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WorkCenterController extends Controller
{
    public function index()
    {
        $this->authorize('manufacturing.read');
        $workCenters = WorkCenter::with('warehouse')->get();
        $warehouses = Warehouse::all();
        return view('erp.manufacturing.work-centers', compact('workCenters', 'warehouses'));
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'capacity' => 'nullable|numeric',
            'hourly_cost' => 'nullable|numeric',
        ]);

        WorkCenter::create($validated);

        return back()->with('success', 'Work center created successfully.');
    }
}

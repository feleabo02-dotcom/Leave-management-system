<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceEquipment;
use App\Models\MaintenanceEquipmentCategory;
use App\Models\MaintenanceRequest;
use App\Models\MaintenancePlan;
use App\Models\Employee;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.read');
        $equipment = MaintenanceEquipment::with('category')->latest()->get();
        $requests = MaintenanceRequest::with(['equipment', 'assignee'])->latest()->take(10)->get();
        $categories = MaintenanceEquipmentCategory::all();
        return view('erp.maintenance.index', compact('equipment', 'requests', 'categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:maintenance_equipment_categories,id',
            'code' => 'required|string|unique:maintenance_equipment,code',
            'status' => 'required|string|max:50',
            'purchase_cost' => 'nullable|numeric',
        ]);

        MaintenanceEquipment::create($validated);

        return back()->with('success', 'Equipment registered successfully.');
    }

    public function show(MaintenanceEquipment $equipment)
    {
        $this->authorize('inventory.read');
        $equipment->load(['category', 'requests']);
        return view('erp.maintenance.show', compact('equipment'));
    }

    public function edit(MaintenanceEquipment $equipment)
    {
        $this->authorize('inventory.update');
        $categories = MaintenanceEquipmentCategory::all();
        $employees = Employee::with('user')->where('status', 'active')->get();
        return view('erp.maintenance.edit', compact('equipment', 'categories', 'employees'));
    }

    public function update(Request $request, MaintenanceEquipment $equipment)
    {
        $this->authorize('inventory.update');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:maintenance_equipment_categories,id',
            'code' => 'required|string|unique:maintenance_equipment,code,' . $equipment->id,
            'status' => 'required|string|max:50',
            'purchase_cost' => 'nullable|numeric',
        ]);

        $equipment->update($validated);

        return redirect()->route('maintenance.index')->with('success', 'Equipment updated successfully.');
    }

    public function destroy(MaintenanceEquipment $equipment)
    {
        $this->authorize('inventory.delete');
        $equipment->delete();
        return back()->with('success', 'Equipment deleted successfully.');
    }

    public function requests()
    {
        $this->authorize('inventory.read');
        $requests = MaintenanceRequest::with(['equipment', 'requester', 'assignee'])->latest()->get();
        return view('erp.maintenance.requests', compact('requests'));
    }

    public function storeRequest(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_id' => 'required|exists:maintenance_equipment,id',
            'description' => 'nullable|string',
            'priority' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'scheduled_date' => 'nullable|date',
        ]);

        MaintenanceRequest::create($validated);

        return back()->with('success', 'Maintenance request created successfully.');
    }

    public function updateRequestStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('inventory.update');
        $validated = $request->validate([
            'stage' => 'required|string|max:50',
        ]);

        $data = ['stage' => $validated['stage']];
        if ($validated['stage'] === 'done') {
            $data['closed_date'] = now();
        }

        $maintenanceRequest->update($data);

        return back()->with('success', 'Request status updated successfully.');
    }
}

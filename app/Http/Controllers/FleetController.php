<?php

namespace App\Http\Controllers;

use App\Models\FleetVehicle;
use App\Models\FleetVehicleModel;
use App\Models\FleetVehicleBrand;
use App\Models\FleetContract;
use App\Models\FleetServiceLog;
use App\Models\FleetFuelLog;
use App\Models\Employee;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.read');
        $vehicles = FleetVehicle::with(['model.brand', 'driver.user'])->latest()->paginate(20);
        return view('erp.fleet.index', compact('vehicles'));
    }

    public function create()
    {
        $this->authorize('inventory.create');
        $brands = FleetVehicleBrand::with('models')->get();
        $employees = Employee::with('user')->where('status', 'active')->get();
        return view('erp.fleet.create', compact('brands', 'employees'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model_id' => 'required|exists:fleet_vehicle_models,id',
            'license_plate' => 'required|string|unique:fleet_vehicles,license_plate',
            'driver_id' => 'nullable|exists:employees,id',
            'acquisition_date' => 'nullable|date',
            'acquisition_cost' => 'nullable|numeric',
            'color' => 'nullable|string|max:50',
            'vin_number' => 'nullable|string',
            'seats' => 'nullable|integer',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        FleetVehicle::create($validated);

        return back()->with('success', 'Vehicle registered successfully.');
    }

    public function show(FleetVehicle $vehicle)
    {
        $this->authorize('inventory.read');
        $vehicle->load(['model.brand', 'driver.user', 'contracts', 'serviceLogs', 'fuelLogs']);
        return view('erp.fleet.show', compact('vehicle'));
    }

    public function edit(FleetVehicle $vehicle)
    {
        $this->authorize('inventory.update');
        $brands = FleetVehicleBrand::with('models')->get();
        $employees = Employee::with('user')->where('status', 'active')->get();
        return view('erp.fleet.edit', compact('vehicle', 'brands', 'employees'));
    }

    public function update(Request $request, FleetVehicle $vehicle)
    {
        $this->authorize('inventory.update');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model_id' => 'required|exists:fleet_vehicle_models,id',
            'license_plate' => 'required|string|unique:fleet_vehicles,license_plate,' . $vehicle->id,
            'driver_id' => 'nullable|exists:employees,id',
            'acquisition_date' => 'nullable|date',
            'acquisition_cost' => 'nullable|numeric',
            'color' => 'nullable|string|max:50',
            'vin_number' => 'nullable|string',
            'seats' => 'nullable|integer',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('fleet.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(FleetVehicle $vehicle)
    {
        $this->authorize('inventory.delete');
        $vehicle->delete();
        return back()->with('success', 'Vehicle deleted successfully.');
    }
}

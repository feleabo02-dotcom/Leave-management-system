<?php

namespace App\Http\Controllers;

use App\Models\FleetFuelLog;
use Illuminate\Http\Request;

class FleetFuelController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:fleet_vehicles,id',
            'date' => 'required|date',
            'liters' => 'required|numeric',
            'cost' => 'required|numeric',
            'odometer' => 'nullable|numeric',
            'fuel_type' => 'nullable|string|max:50',
        ]);

        FleetFuelLog::create($validated);

        return back()->with('success', 'Fuel log added successfully.');
    }

    public function destroy(FleetFuelLog $fuelLog)
    {
        $this->authorize('inventory.delete');
        $fuelLog->delete();
        return back()->with('success', 'Fuel log deleted successfully.');
    }
}

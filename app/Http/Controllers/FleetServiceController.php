<?php

namespace App\Http\Controllers;

use App\Models\FleetServiceLog;
use Illuminate\Http\Request;

class FleetServiceController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:fleet_vehicles,id',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'cost' => 'nullable|numeric',
            'odometer' => 'nullable|numeric',
        ]);

        FleetServiceLog::create($validated);

        return back()->with('success', 'Service log added successfully.');
    }

    public function destroy(FleetServiceLog $serviceLog)
    {
        $this->authorize('inventory.delete');
        $serviceLog->delete();
        return back()->with('success', 'Service log deleted successfully.');
    }
}

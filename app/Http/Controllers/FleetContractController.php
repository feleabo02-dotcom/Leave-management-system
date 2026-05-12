<?php

namespace App\Http\Controllers;

use App\Models\FleetContract;
use Illuminate\Http\Request;

class FleetContractController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:fleet_vehicles,id',
            'type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'cost' => 'nullable|numeric',
        ]);

        FleetContract::create($validated);

        return back()->with('success', 'Contract added successfully.');
    }

    public function destroy(FleetContract $contract)
    {
        $this->authorize('inventory.delete');
        $contract->delete();
        return back()->with('success', 'Contract deleted successfully.');
    }
}

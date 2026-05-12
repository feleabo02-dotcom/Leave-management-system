<?php

namespace App\Http\Controllers;

use App\Models\Routing;
use App\Models\RoutingStep;
use App\Models\Bom;
use App\Models\WorkCenter;
use Illuminate\Http\Request;

class RoutingController extends Controller
{
    public function index()
    {
        $this->authorize('manufacturing.read');
        $routings = Routing::with(['bom', 'steps'])->get();
        $workCenters = WorkCenter::all();
        $boms = Bom::all();
        return view('erp.manufacturing.routings', compact('routings', 'workCenters', 'boms'));
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bom_id' => 'required|exists:bill_of_materials,id',
            'lead_time' => 'nullable|numeric',
        ]);

        Routing::create($validated);

        return back()->with('success', 'Routing created successfully.');
    }

    public function addStep(Request $request, Routing $routing)
    {
        $this->authorize('manufacturing.update');
        $validated = $request->validate([
            'work_center_id' => 'required|exists:work_centers,id',
            'sequence' => 'required|integer|min:0',
            'name' => 'required|string|max:255',
            'hours' => 'nullable|numeric',
        ]);

        RoutingStep::create([
            'routing_id' => $routing->id,
            'work_center_id' => $validated['work_center_id'],
            'sequence' => $validated['sequence'],
            'name' => $validated['name'],
            'hours' => $validated['hours'] ?? 0,
        ]);

        return back()->with('success', 'Step added successfully.');
    }
}

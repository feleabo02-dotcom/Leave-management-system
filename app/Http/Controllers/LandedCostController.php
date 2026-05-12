<?php

namespace App\Http\Controllers;

use App\Models\LandedCost;
use App\Models\LandedCostLine;
use Illuminate\Http\Request;

class LandedCostController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.read');
        $landedCosts = LandedCost::with('lines')->latest()->get();

        return view('erp.inventory.landed-costs', compact('landedCosts'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'nullable|date',
            'amount_total' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['state'] = 'draft';
        LandedCost::create($validated);

        return back()->with('success', 'Landed cost created successfully.');
    }

    public function validateCost(LandedCost $landedCost)
    {
        $this->authorize('inventory.update');
        $landedCost->update(['state' => 'validated']);

        return back()->with('success', 'Landed cost validated successfully.');
    }
}

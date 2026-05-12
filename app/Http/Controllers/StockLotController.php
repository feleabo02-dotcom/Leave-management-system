<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLot;
use Illuminate\Http\Request;

class StockLotController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.read');
        $lots = StockLot::with('product')->latest()->get();

        return view('erp.inventory.lots', compact('lots'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'expiration_date' => 'nullable|date',
            'best_before_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        StockLot::create($validated);

        return back()->with('success', 'Stock lot created successfully.');
    }
}

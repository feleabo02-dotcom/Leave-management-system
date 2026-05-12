<?php

namespace App\Http\Controllers;

use App\Models\PickingBatch;
use Illuminate\Http\Request;

class PickingBatchController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.read');
        $batches = PickingBatch::with('user')->latest()->get();

        return view('erp.inventory.batches', compact('batches'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'scheduled_date' => 'nullable|date',
            'is_wave' => 'nullable|boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['state'] = 'draft';
        PickingBatch::create($validated);

        return back()->with('success', 'Picking batch created successfully.');
    }

    public function complete(PickingBatch $pickingBatch)
    {
        $this->authorize('inventory.update');
        $pickingBatch->update(['state' => 'done']);

        return back()->with('success', 'Picking batch completed successfully.');
    }
}

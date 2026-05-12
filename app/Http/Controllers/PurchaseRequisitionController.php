<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    public function index()
    {
        $this->authorize('procurement.read');
        $requisitions = PurchaseRequisition::with(['requester', 'lines'])->latest()->get();

        return view('erp.procurement.requisitions', compact('requisitions'));
    }

    public function store(Request $request)
    {
        $this->authorize('procurement.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_date' => 'nullable|date',
        ]);

        $validated['status'] = 'draft';
        $validated['requested_by'] = auth()->id();
        PurchaseRequisition::create($validated);

        return back()->with('success', 'Purchase requisition created successfully.');
    }

    public function approve(PurchaseRequisition $purchaseRequisition)
    {
        $this->authorize('procurement.update');
        $purchaseRequisition->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Purchase requisition approved successfully.');
    }

    public function reject(PurchaseRequisition $purchaseRequisition)
    {
        $this->authorize('procurement.update');
        $purchaseRequisition->update(['status' => 'rejected']);

        return back()->with('success', 'Purchase requisition rejected successfully.');
    }
}

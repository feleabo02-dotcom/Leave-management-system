<?php

namespace App\Http\Controllers;

use App\Models\PurchaseAgreement;
use App\Models\Vendor;
use Illuminate\Http\Request;

class PurchaseAgreementController extends Controller
{
    public function index()
    {
        $this->authorize('procurement.read');
        $agreements = PurchaseAgreement::with(['vendor', 'lines'])->latest()->get();

        return view('erp.procurement.agreements', compact('agreements'));
    }

    public function store(Request $request)
    {
        $this->authorize('procurement.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vendor_id' => 'required|exists:vendors,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'terms' => 'nullable|string',
        ]);

        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();
        PurchaseAgreement::create($validated);

        return back()->with('success', 'Purchase agreement created successfully.');
    }

    public function activate(PurchaseAgreement $purchaseAgreement)
    {
        $this->authorize('procurement.update');
        $purchaseAgreement->update(['status' => 'active']);

        return back()->with('success', 'Purchase agreement activated successfully.');
    }

    public function close(PurchaseAgreement $purchaseAgreement)
    {
        $this->authorize('procurement.update');
        $purchaseAgreement->update(['status' => 'closed']);

        return back()->with('success', 'Purchase agreement closed successfully.');
    }
}

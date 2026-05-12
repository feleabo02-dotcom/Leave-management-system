<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Account;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $this->authorize('accounting.read');
        $taxes = Tax::with('account')->get();
        $accounts = Account::all();
        return view('erp.accounting.taxes', compact('taxes', 'accounts'));
    }

    public function store(Request $request)
    {
        $this->authorize('accounting.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|string|max:50',
            'account_id' => 'required|exists:account_accounts,id',
        ]);

        Tax::create($validated);

        return back()->with('success', 'Tax created successfully.');
    }

    public function destroy(Tax $tax)
    {
        $this->authorize('accounting.delete');
        $tax->delete();
        return back()->with('success', 'Tax deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function index()
    {
        $this->authorize('accounting.read');
        $accounts = Account::with('journalItems')->get();
        $recentEntries = JournalEntry::with('journal')->latest()->take(10)->get();
        
        return view('erp.accounting.index', compact('accounts', 'recentEntries'));
    }

    public function coa()
    {
        $this->authorize('accounting.read');
        $accounts = Account::orderBy('code')->get();
        return view('erp.accounting.coa', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $this->authorize('accounting.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:accounts,code',
            'type' => 'required|in:receivable,payable,bank,cash,income,expense,asset,liability,equity',
        ]);

        Account::create($validated);

        return back()->with('success', 'Account added to COA.');
    }

    public function journals()
    {
        $this->authorize('accounting.read');
        $journals = Journal::withCount('entries')->get();
        return view('erp.accounting.journals', compact('journals'));
    }
}

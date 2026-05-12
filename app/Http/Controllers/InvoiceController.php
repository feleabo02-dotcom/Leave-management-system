<?php

namespace App\Http\Controllers;

use App\Models\AccountInvoice;
use App\Models\AccountPayment;
use App\Models\Customer;
use App\Models\Journal;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $this->authorize('accounting.read');
        $invoices = AccountInvoice::with('partner')->latest()->paginate(20);
        $customers = Customer::all();
        $journals = Journal::all();
        return view('erp.accounting.invoices', compact('invoices', 'customers', 'journals'));
    }

    public function store(Request $request)
    {
        $this->authorize('accounting.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:out_invoice,in_invoice,out_refund,in_refund',
            'partner_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'amount_total' => 'required|numeric',
            'amount_untaxed' => 'required|numeric',
            'journal_id' => 'required|exists:journals,id',
        ]);

        AccountInvoice::create($validated);

        return back()->with('success', 'Invoice created successfully.');
    }

    public function show(AccountInvoice $invoice)
    {
        $this->authorize('accounting.read');
        $invoice->load(['partner', 'journal', 'payments']);
        return view('erp.accounting.invoice-show', compact('invoice'));
    }

    public function validateInvoice(AccountInvoice $invoice)
    {
        $this->authorize('accounting.update');
        $invoice->update(['status' => 'posted']);
        return back()->with('success', 'Invoice validated successfully.');
    }

    public function registerPayment(Request $request, AccountInvoice $invoice)
    {
        $this->authorize('accounting.create');
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric',
            'method' => 'required|string|max:50',
            'account_id' => 'required|exists:account_accounts,id',
        ]);

        AccountPayment::create([
            'invoice_id' => $invoice->id,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'account_id' => $validated['account_id'],
        ]);

        $invoice->update(['status' => 'paid']);

        return back()->with('success', 'Payment registered successfully.');
    }
}

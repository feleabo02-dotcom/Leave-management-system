<?php

namespace App\Http\Controllers;

use App\Models\CrmActivity;
use App\Models\Customer;
use App\Models\Opportunity;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function index()
    {
        $this->authorize('crm.read');
        $customers = Customer::withCount('opportunities')->latest()->paginate(20);
        return view('erp.crm.index', compact('customers'));
    }

    public function pipeline()
    {
        $this->authorize('crm.read');
        $opportunities = Opportunity::with(['customer', 'assignee'])->get()->groupBy('stage');
        $stages = ['new', 'qualified', 'proposition', 'won', 'lost'];
        
        return view('erp.crm.pipeline', compact('opportunities', 'stages'));
    }

    public function storeCustomer(Request $request)
    {
        $this->authorize('crm.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'type' => 'required|in:lead,customer',
        ]);

        Customer::create($validated);

        return back()->with('success', 'Customer record created.');
    }

    public function storeOpportunity(Request $request)
    {
        $this->authorize('crm.create');
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'expected_revenue' => 'required|numeric|min:0',
            'closing_date' => 'nullable|date',
        ]);

        $validated['assigned_to'] = auth()->id();
        $validated['stage'] = 'new';
        $validated['probability'] = 10;

        Opportunity::create($validated);

        return back()->with('success', 'Opportunity added to pipeline.');
    }

    public function updateStage(Request $request, Opportunity $opportunity)
    {
        $this->authorize('crm.update');
        $request->validate(['stage' => 'required|in:new,qualified,proposition,won,lost']);

        $probabilities = [
            'new' => 10,
            'qualified' => 30,
            'proposition' => 70,
            'won' => 100,
            'lost' => 0
        ];

        $opportunity->update([
            'stage' => $request->stage,
            'probability' => $probabilities[$request->stage]
        ]);

        return back()->with('success', 'Pipeline stage updated.');
    }

    public function logActivity(Request $request, Opportunity $opportunity)
    {
        $this->authorize('crm.update');
        $request->validate([
            'type' => 'required|in:call,email,meeting,task',
            'notes' => 'required|string',
        ]);

        CrmActivity::create([
            'opportunity_id' => $opportunity->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'date' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Activity logged.');
    }

    public function showOpportunity(Opportunity $opportunity)
    {
        $opportunity->load(['customer', 'activities.user']);
        return view('erp.crm.opportunity-show', compact('opportunity'));
    }
}

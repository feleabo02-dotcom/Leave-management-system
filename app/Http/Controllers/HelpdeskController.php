<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskResponse;
use App\Models\User;
use Illuminate\Http\Request;

class HelpdeskController extends Controller
{
    public function index()
    {
        $this->authorize('helpdesk.read');
        $tickets = HelpdeskTicket::with(['category', 'assignedTo', 'creator'])
            ->latest()
            ->paginate(20);
        $categories = HelpdeskCategory::all();
        $agents = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['admin', 'super_admin', 'manager', 'hr_manager']))->get();

        return view('erp.helpdesk.index', compact('tickets', 'categories', 'agents'));
    }

    public function store(Request $request)
    {
        $this->authorize('helpdesk.create');
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:helpdesk_categories,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $count = HelpdeskTicket::count() + 1;
        $validated['ticket_number'] = 'HDT-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();

        HelpdeskTicket::create($validated);

        return back()->with('success', 'Ticket created successfully.');
    }

    public function show(HelpdeskTicket $helpdeskTicket)
    {
        $this->authorize('helpdesk.read');
        $helpdeskTicket->load(['category', 'assignedTo', 'creator', 'responses.user']);

        return view('erp.helpdesk.show', compact('helpdeskTicket'));
    }

    public function assign(Request $request, HelpdeskTicket $helpdeskTicket)
    {
        $this->authorize('helpdesk.update');
        $request->validate(['assigned_to' => 'required|exists:users,id']);

        $helpdeskTicket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'open',
        ]);

        HelpdeskResponse::create([
            'ticket_id' => $helpdeskTicket->id,
            'user_id' => auth()->id(),
            'body' => 'Ticket assigned to ' . User::find($request->assigned_to)->name,
            'is_internal' => true,
        ]);

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function status(Request $request, HelpdeskTicket $helpdeskTicket)
    {
        $this->authorize('helpdesk.update');
        $request->validate(['status' => 'required|in:new,open,pending,resolved,closed']);

        $data = ['status' => $request->status];
        if ($request->status === 'resolved') $data['resolved_at'] = now();
        if ($request->status === 'closed') $data['closed_at'] = now();

        $helpdeskTicket->update($data);

        HelpdeskResponse::create([
            'ticket_id' => $helpdeskTicket->id,
            'user_id' => auth()->id(),
            'body' => 'Status changed to ' . $request->status,
            'is_internal' => true,
        ]);

        return back()->with('success', 'Ticket status updated.');
    }

    public function respond(Request $request, HelpdeskTicket $helpdeskTicket)
    {
        $this->authorize('helpdesk.update');
        $request->validate([
            'body' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        HelpdeskResponse::create([
            'ticket_id' => $helpdeskTicket->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
            'is_internal' => $request->boolean('is_internal'),
        ]);

        if (!$request->boolean('is_internal') && $helpdeskTicket->status === 'new') {
            $helpdeskTicket->update(['status' => 'open']);
        }

        return back()->with('success', 'Response added.');
    }
}

<x-layouts.erp :title="'Helpdesk'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Helpdesk</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage support tickets and customer inquiries.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addTicketModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Ticket
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\HelpdeskTicket::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">New</p>
            <p class="text-2xl font-bold text-blue-600">{{ \App\Models\HelpdeskTicket::where('status', 'new')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Open</p>
            <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\HelpdeskTicket::where('status', 'open')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Pending</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\HelpdeskTicket::where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Resolved</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\HelpdeskTicket::whereIn('status', ['resolved', 'closed'])->count() }}</p>
        </div>
    </div>

    {{-- Ticket Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex gap-2">
                <select id="filterCategory" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select id="filterStatus" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="">All Status</option>
                    <option value="new">New</option>
                    <option value="open">Open</option>
                    <option value="pending">Pending</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
                <select id="filterPriority" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="">All Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="relative w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search tickets..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Ticket</th>
                        <th class="px-5 py-4 border-b border-gray-100">Category</th>
                        <th class="px-5 py-4 border-b border-gray-100">Priority</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Assigned To</th>
                        <th class="px-5 py-4 border-b border-gray-100">Created</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <a href="{{ route('helpdesk.show', $ticket) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600">{{ $ticket->subject }}</a>
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-tight">{{ $ticket->ticket_number }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $ticket->category?->name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($ticket->priority === 'urgent') bg-red-100 text-red-700
                                    @elseif($ticket->priority === 'high') bg-orange-100 text-orange-700
                                    @elseif($ticket->priority === 'medium') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($ticket->status === 'new') bg-blue-100 text-blue-700
                                    @elseif($ticket->status === 'open') bg-indigo-100 text-indigo-700
                                    @elseif($ticket->status === 'pending') bg-orange-100 text-orange-700
                                    @elseif($ticket->status === 'resolved') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                @if($ticket->assignedTo)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($ticket->assignedTo->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700 font-medium">{{ $ticket->assignedTo->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-400">{{ $ticket->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('helpdesk.show', $ticket) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                                    @if(!$ticket->assigned_to)
                                        <button onclick="openAssignModal('{{ $ticket->id }}', '{{ $ticket->ticket_number }}')" class="text-gray-400 hover:text-indigo-600 transition p-1.5" title="Assign"><i class="ph ph-user-plus text-lg"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No tickets created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    {{-- Create Ticket Modal --}}
    <div id="addTicketModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addTicketModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('helpdesk.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Ticket</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Subject</label>
                                <input type="text" name="subject" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Email not sending">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" required rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Describe the issue..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category</label>
                                <select name="category_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Priority</label>
                                <select name="priority" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Customer Name</label>
                                <input type="text" name="customer_name" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Customer Email</label>
                                <input type="email" name="customer_email" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Customer Phone</label>
                                <input type="text" name="customer_phone" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Ticket</button>
                        <button type="button" onclick="document.getElementById('addTicketModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Assign Modal --}}
    <div id="assignModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelled by="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('assignModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="assignForm" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Assign Ticket</h3>
                        <p class="text-sm text-gray-500 mb-4" id="assignTicketInfo"></p>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Support Agent</label>
                            <select name="assigned_to" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="">Select Agent...</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->roles->first()?->name ?? 'Staff' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Assign</button>
                        <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAssignModal(ticketId, ticketNumber) {
            document.getElementById('assignTicketInfo').innerText = 'Ticket: ' + ticketNumber;
            document.getElementById('assignForm').action = '/helpdesk/' + ticketId + '/assign';
            document.getElementById('assignModal').classList.remove('hidden');
        }
    </script>
</x-layouts.erp>
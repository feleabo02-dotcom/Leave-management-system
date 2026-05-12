<x-layouts.erp :title="'Ticket - ' . $helpdeskTicket->ticket_number">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('helpdesk.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $helpdeskTicket->subject }}</h1>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                    @if($helpdeskTicket->status === 'new') bg-blue-100 text-blue-700
                    @elseif($helpdeskTicket->status === 'open') bg-indigo-100 text-indigo-700
                    @elseif($helpdeskTicket->status === 'pending') bg-orange-100 text-orange-700
                    @elseif($helpdeskTicket->status === 'resolved') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-600
                    @endif">
                    {{ $helpdeskTicket->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">{{ $helpdeskTicket->ticket_number }} &middot; Created {{ $helpdeskTicket->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="flex gap-2">
            @can('helpdesk.update')
                @if(!$helpdeskTicket->assigned_to)
                    <button onclick="openAssignModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-user-plus"></i> Assign
                    </button>
                @endif
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-arrows-clockwise"></i> Update Status
                        <i class="ph ph-caret-down text-xs"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">
                        @foreach(['new', 'open', 'pending', 'resolved', 'closed'] as $s)
                            <form action="{{ route('helpdesk.status', $helpdeskTicket) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="{{ $s }}">
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition {{ $helpdeskTicket->status === $s ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                    {{ ucfirst($s) }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Details Sidebar --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Ticket Details</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Priority</p>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                            @if($helpdeskTicket->priority === 'urgent') bg-red-100 text-red-700
                            @elseif($helpdeskTicket->priority === 'high') bg-orange-100 text-orange-700
                            @elseif($helpdeskTicket->priority === 'medium') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-600
                            @endif">
                            {{ $helpdeskTicket->priority }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Category</p>
                        <p class="text-sm font-medium text-gray-900">{{ $helpdeskTicket->category?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Created By</p>
                        <p class="text-sm font-medium text-gray-900">{{ $helpdeskTicket->creator?->name ?? '—' }}</p>
                    </div>
                    @if($helpdeskTicket->assignedTo)
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Assigned To</p>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                    {{ strtoupper(substr($helpdeskTicket->assignedTo->name, 0, 2)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $helpdeskTicket->assignedTo->name }}</span>
                            </div>
                        </div>
                    @endif
                    @if($helpdeskTicket->resolved_at)
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Resolved At</p>
                            <p class="text-sm font-medium text-gray-900">{{ $helpdeskTicket->resolved_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif
                    @if($helpdeskTicket->closed_at)
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Closed At</p>
                            <p class="text-sm font-medium text-gray-900">{{ $helpdeskTicket->closed_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($helpdeskTicket->customer_name || $helpdeskTicket->customer_email)
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-4">Customer Info</h3>
                    <div class="space-y-3">
                        @if($helpdeskTicket->customer_name)
                            <div>
                                <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider mb-0.5">Name</p>
                                <p class="text-sm font-medium text-indigo-900">{{ $helpdeskTicket->customer_name }}</p>
                            </div>
                        @endif
                        @if($helpdeskTicket->customer_email)
                            <div>
                                <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider mb-0.5">Email</p>
                                <p class="text-sm font-medium text-indigo-900">{{ $helpdeskTicket->customer_email }}</p>
                            </div>
                        @endif
                        @if($helpdeskTicket->customer_phone)
                            <div>
                                <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider mb-0.5">Phone</p>
                                <p class="text-sm font-medium text-indigo-900">{{ $helpdeskTicket->customer_phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Description & Responses --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Original Description --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Description</h3>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $helpdeskTicket->description }}</p>
            </div>

            {{-- Responses --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Responses ({{ $helpdeskTicket->responses->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($helpdeskTicket->responses as $response)
                        <div class="px-5 py-4 {{ $response->is_internal ? 'bg-yellow-50/50' : '' }}">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold
                                    {{ $response->is_internal ? 'bg-yellow-100 text-yellow-700' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ strtoupper(substr($response->user->name, 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-gray-900">{{ $response->user->name }}</p>
                                            @if($response->is_internal)
                                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-yellow-100 text-yellow-700 uppercase tracking-wider">Internal</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-400">{{ $response->created_at->diffForHumans() }}</p>
                                    </div>
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $response->body }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-12 text-center text-gray-400 text-sm">No responses yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Add Response --}}
            @can('helpdesk.update')
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Add Response</h3>
                    <form action="{{ route('helpdesk.respond', $helpdeskTicket) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <textarea name="body" required rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Type your response..."></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Internal Note</span>
                            </label>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                                <i class="ph ph-paper-plane-right"></i> Send Response
                            </button>
                        </div>
                    </form>
                </div>
            @endcan
        </div>
    </div>

    {{-- Assign Modal --}}
    <div id="assignModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('assignModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('helpdesk.assign', $helpdeskTicket) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Assign Ticket</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ $helpdeskTicket->ticket_number }} &mdash; {{ $helpdeskTicket->subject }}</p>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Support Agent</label>
                            <select name="assigned_to" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="">Select Agent...</option>
                                @foreach($agents ?? \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('slug', ['admin', 'super_admin']))->get() as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
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
        function openAssignModal() {
            document.getElementById('assignModal').classList.remove('hidden');
        }
    </script>
</x-layouts.erp>
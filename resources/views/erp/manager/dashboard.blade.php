<x-layouts.erp :title="'Manager Dashboard'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Time Off Approval</h1>
            <p class="text-sm text-gray-500 mt-0.5">Review and manage your team's leave requests.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('manager.reports.by-employee') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-chart-bar"></i> Reports
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending approvals</p>
            <div class="flex items-end justify-between mt-2">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['pending'] }}</span>
                @if($stats['pending'] > 0)
                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">{{ $stats['pending'] > 3 ? 'Urgent' : 'Review' }}</span>
                @endif
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved today</p>
            <div class="flex items-end justify-between mt-2">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['approved_today'] }}</span>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Team size</p>
            <div class="flex items-end justify-between mt-2">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['team_size'] }}</span>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Upcoming absences</p>
            <div class="flex items-end justify-between mt-2">
                <span class="text-3xl font-bold text-gray-900">{{ $pendingRequests->where('status', 'approved')->count() }}</span>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
        {{-- Kanban Approval Queue --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approval queue</p>
                    <h2 class="mt-1 text-xl font-bold text-gray-900">Pending requests</h2>
                </div>
                @if($pendingRequests->count() > 1)
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm" onclick="document.getElementById('bulk-approve-form').submit()">
                        Approve All
                    </button>
                @endif
            </div>

            @if(session('status') === 'manager-approved')
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
                    <i class="ph ph-check-circle text-lg"></i> Request approved by manager.
                </div>
            @elseif(session('status') === 'hr-approved')
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
                    <i class="ph ph-check-circle text-lg"></i> Request approved by HR.
                </div>
            @elseif(session('status') === 'request-rejected')
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 flex items-center gap-2">
                    <i class="ph ph-x-circle text-lg"></i> Request rejected.
                </div>
            @endif

            <form id="bulk-approve-form" method="POST" action="{{ route('manager.approvals.manager', '__bulk__') }}" class="hidden">
                @csrf
            </form>

            <div class="space-y-4">
                @forelse($pendingRequests as $request)
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 hover:border-indigo-200 hover:bg-indigo-50/30 transition">
                        <div class="flex items-start gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($request->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $request->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $request->user->department?->name ?? '—' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-600 ml-13 pl-1">
                                    <span class="flex items-center gap-1.5">
                                        <i class="ph ph-briefcase text-indigo-400"></i>
                                        {{ $request->leaveType->name }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="ph ph-calendar text-indigo-400"></i>
                                        {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d') }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="ph ph-clock text-indigo-400"></i>
                                        {{ $request->days }} day{{ $request->days != 1 ? 's' : '' }}
                                    </span>
                                </div>
                                @if($request->reason)
                                    <p class="mt-2 text-xs text-gray-500 ml-13 pl-1 truncate">{{ $request->reason }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                @php
                                    $needsManager = $request->manager_status === 'pending';
                                    $needsHr = $request->hr_status === 'pending' && in_array($request->leaveType->validation_type, ['hr', 'both'], true);
                                @endphp

                                @if($request->status === 'manager_approved')
                                    <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">Manager OK</span>
                                @elseif($request->status === 'submitted')
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                                @endif

                                <div class="flex items-center gap-1">
                                    @if($needsManager || $needsHr)
                                        <form method="POST" action="{{ route($needsHr && !$needsManager ? 'manager.approvals.hr' : 'manager.approvals.manager', $request) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition" title="Approve">
                                                <i class="ph ph-check-circle text-xl"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" onclick="document.getElementById('reject-modal-{{ $request->id }}').classList.remove('hidden')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Reject">
                                        <i class="ph ph-x-circle text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reject Modal --}}
                    <div id="reject-modal-{{ $request->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="if(event.target===this)this.classList.add('hidden')">
                        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4" onclick="event.stopPropagation()">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Reject Request</h3>
                            <p class="text-sm text-gray-500 mb-4">{{ $request->user->name }} - {{ $request->leaveType->name }} ({{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d') }})</p>
                            <form method="POST" action="{{ route('manager.approvals.reject', $request) }}">
                                @csrf
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                                <textarea name="reason" rows="3" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Why is this being rejected?"></textarea>
                                <div class="flex justify-end gap-2 mt-4">
                                    <button type="button" onclick="document.getElementById('reject-modal-{{ $request->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">Reject</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="ph ph-check-circle text-3xl text-green-600"></i>
                        </div>
                        <p class="font-semibold text-gray-900">All caught up!</p>
                        <p class="text-sm text-gray-500 mt-1">No pending leave requests to review.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Team Calendar</p>
                <h3 class="mt-1 text-lg font-bold text-gray-900">Upcoming</h3>
                <div class="mt-4 space-y-3">
                    @forelse($teamCalendar->flatten(1)->sortBy('start_date')->take(5) as $req)
                        <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($req->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $req->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $req->leaveType->name }} - {{ $req->start_date->format('M d') }} to {{ $req->end_date->format('M d') }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $req->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($req->status === 'manager_approved' ? 'HR Pending' : $req->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No upcoming absences.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Workflow</p>
                <h3 class="mt-1 text-lg font-bold text-gray-900">Approval flow</h3>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span class="text-gray-600">Submitted</span>
                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">{{ $pendingRequests->where('status', 'submitted')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span class="text-gray-600">Manager review</span>
                        <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">{{ $pendingRequests->where('status', 'manager_approved')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span class="text-gray-600">Approved</span>
                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">{{ $history->where('status', 'approved')->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Quick Links</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('manager.reports.by-employee') }}" class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <i class="ph ph-chart-bar text-lg text-indigo-600"></i>
                        By Employee Report
                    </a>
                    <a href="{{ route('manager.reports.summary') }}" class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <i class="ph ph-file-text text-lg text-indigo-600"></i>
                        Summary Report
                    </a>
                    <a href="{{ route('manager.reports.balance') }}" class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <i class="ph ph-scales text-lg text-indigo-600"></i>
                        Balance Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>

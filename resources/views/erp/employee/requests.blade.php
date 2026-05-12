<x-layouts.erp :title="'My Requests'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Requests</h1>
            <p class="text-sm text-gray-500 mt-0.5">View and track your leave requests.</p>
        </div>
        <a href="{{ route('leave-requests.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i class="ph ph-plus"></i> New Request
        </a>
    </div>

    @if(session('status') === 'request-submitted')
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            Request submitted successfully.
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex rounded-lg border border-gray-200 bg-gray-50 p-1">
                <button class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600">All</button>
                <button class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700">Pending</button>
                <button class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700">Approved</button>
                <button class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700">Rejected</button>
            </div>
            <div class="flex-1"></div>
            <select class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option>All time</option>
                <option>This month</option>
                <option>Last 3 months</option>
                <option>This year</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                        <th class="px-6 py-3 font-medium">Leave Type</th>
                        <th class="px-6 py-3 font-medium">Dates</th>
                        <th class="px-6 py-3 font-medium">Days</th>
                        <th class="px-6 py-3 font-medium">Workflow</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                                        {{ strtoupper(substr($request->leaveType->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900 text-sm">{{ $request->leaveType->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $request->start_date->format('M d, Y') }} — {{ $request->end_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $request->days }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $workflow = match ($request->leaveType->validation_type) {
                                        'both' => 'Manager + HR',
                                        'hr' => 'HR only',
                                        'no' => 'Auto',
                                        default => 'Manager',
                                    };
                                @endphp
                                <span class="text-sm text-gray-500">{{ $workflow }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $request->status === 'approved' ? 'bg-green-100 text-green-700' : ($request->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                <p class="font-semibold text-gray-900 mb-1">No leave requests yet</p>
                                <p class="text-xs">Click "New Request" to submit your first leave.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.erp>

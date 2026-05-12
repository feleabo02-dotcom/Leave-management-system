<x-layouts.erp :title="'Balance Report'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Balance Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Employee leave balances for {{ $year }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search employee..." class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-48">
                <select name="year" onchange="this.form.submit()" class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $y === $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Search</button>
                @if($search)
                    <a href="{{ route('manager.reports.balance', ['year' => $year]) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                        <th class="px-6 py-3 font-medium">Employee</th>
                        @foreach($leaveTypes as $type)
                            <th class="px-4 py-3 font-medium text-center">{{ $type->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                        {{ strtoupper(substr($employee->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $employee->name }}</span>
                                </div>
                            </td>
                            @foreach($leaveTypes as $type)
                                @php
                                    $alloc = $employee->leaveAllocations->firstWhere('leave_type_id', $type->id);
                                    $remaining = $alloc ? $alloc->remaining_days : 0;
                                @endphp
                                <td class="px-4 py-4 text-center">
                                    @if($alloc)
                                        <span class="font-semibold {{ $remaining <= 0 ? 'text-red-600' : ($remaining < 2 ? 'text-amber-600' : 'text-green-600') }}">
                                            {{ number_format($remaining, 1) }}
                                        </span>
                                        <span class="text-xs text-gray-400 block">/ {{ number_format(($alloc->total_allocated_days ?: $alloc->allocated_days + $alloc->carried_over_days), 1) }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($leaveTypes) + 1 }}" class="px-6 py-8 text-center text-gray-500 text-sm">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.erp>

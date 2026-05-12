<x-layouts.erp :title="'Summary Report'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Summary Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Organization-wide leave overview for {{ $year }}.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y === $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mb-6">
        @foreach($summary as $row)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $row['type']->name }}</p>
                    @if($row['type']->color)
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $row['type']->color }}"></span>
                    @endif
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Allocated</span>
                        <span class="font-semibold text-gray-900">{{ number_format($row['total_allocated'], 1) }} days</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Used</span>
                        <span class="font-semibold {{ $row['total_used'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ number_format($row['total_used'], 1) }} days</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Pending</span>
                        <span class="font-semibold text-yellow-600">{{ number_format($row['total_pending'], 1) }} days</span>
                    </div>
                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-gray-600 font-medium">Remaining</span>
                        <span class="font-semibold {{ $row['total_remaining'] <= 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($row['total_remaining'], 1) }} days</span>
                    </div>
                </div>
                <div class="mt-3 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    @php $pct = $row['total_allocated'] > 0 ? min(100, ($row['total_used'] / $row['total_allocated']) * 100) : 0; @endphp
                    <div class="h-full rounded-full {{ $pct > 80 ? 'bg-red-400' : ($pct > 60 ? 'bg-amber-400' : 'bg-indigo-500') }}" style="width: {{ $pct }}%"></div>
                </div>
                <p class="mt-2 text-xs text-gray-400">{{ $row['employee_count'] }} employee(s) enrolled</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                        <th class="px-6 py-3 font-medium">Leave Type</th>
                        <th class="px-6 py-3 font-medium text-right">Employees</th>
                        <th class="px-6 py-3 font-medium text-right">Total Allocated</th>
                        <th class="px-6 py-3 font-medium text-right">Total Used</th>
                        <th class="px-6 py-3 font-medium text-right">Remaining</th>
                        <th class="px-6 py-3 font-medium text-right">Utilization</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($summary as $row)
                        @php $pct = $row['total_allocated'] > 0 ? round(($row['total_used'] / $row['total_allocated']) * 100) : 0; @endphp
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($row['type']->color)
                                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $row['type']->color }}"></span>
                                    @endif
                                    <span class="font-medium text-gray-900">{{ $row['type']->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-700">{{ $row['employee_count'] }}</td>
                            <td class="px-6 py-4 text-right text-gray-700">{{ number_format($row['total_allocated'], 1) }}</td>
                            <td class="px-6 py-4 text-right {{ $row['total_used'] > 0 ? 'text-amber-600 font-medium' : 'text-gray-700' }}">{{ number_format($row['total_used'], 1) }}</td>
                            <td class="px-6 py-4 text-right {{ $row['total_remaining'] <= 0 ? 'text-red-600 font-medium' : 'text-green-600 font-medium' }}">{{ number_format($row['total_remaining'], 1) }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $pct > 80 ? 'bg-red-100 text-red-700' : ($pct > 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">{{ $pct }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.erp>

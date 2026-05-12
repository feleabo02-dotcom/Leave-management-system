<x-layouts.erp :title="'By Employee Report'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">By Employee</h1>
            <p class="text-sm text-gray-500 mt-0.5">Leave allocation and usage per employee.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y === $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                        <th class="px-6 py-3 font-medium">Employee</th>
                        @foreach($leaveTypes as $type)
                            <th class="px-4 py-3 font-medium text-center" colspan="3">{{ $type->name }}</th>
                        @endforeach
                        <th class="px-4 py-3 font-medium text-center">Total</th>
                    </tr>
                    <tr class="bg-gray-50/50 border-b border-gray-200 text-xs uppercase text-gray-400 tracking-wider">
                        <th class="px-6 py-2"></th>
                        @foreach($leaveTypes as $type)
                            <th class="px-3 py-2 font-medium text-center">Alloc</th>
                            <th class="px-3 py-2 font-medium text-center">Used</th>
                            <th class="px-3 py-2 font-medium text-center">Rem</th>
                        @endforeach
                        <th class="px-3 py-2 font-medium text-center">Remaining</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reportData as $row)
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                        {{ strtoupper(substr($row['employee']->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $row['employee']->name }}</span>
                                </div>
                            </td>
                            @foreach($leaveTypes as $type)
                                @php $d = $row['types'][$type->id] ?? ['allocated' => 0, 'used' => 0, 'remaining' => 0]; @endphp
                                <td class="px-3 py-4 text-center text-gray-700">{{ number_format($d['allocated'], 1) }}</td>
                                <td class="px-3 py-4 text-center {{ $d['used'] > 0 ? 'text-amber-600 font-medium' : 'text-gray-700' }}">{{ number_format($d['used'], 1) }}</td>
                                <td class="px-3 py-4 text-center {{ $d['remaining'] <= 0 ? 'text-red-600 font-medium' : 'text-green-600 font-medium' }}">{{ number_format($d['remaining'], 1) }}</td>
                            @endforeach
                            @php $totalRemaining = collect($row['types'])->sum('remaining'); @endphp
                            <td class="px-3 py-4 text-center font-semibold {{ $totalRemaining <= 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($totalRemaining, 1) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $leaveTypes->count() * 3 + 2 }}" class="px-6 py-8 text-center text-gray-500 text-sm">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.erp>

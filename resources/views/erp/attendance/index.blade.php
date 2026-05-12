<x-layouts.erp :title="'Attendance Management'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Attendance Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Monitor and manage employee daily attendance logs.</p>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-file-csv"></i> Export CSV
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Manual Entry
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Present Today</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Attendance::where('date', now()->toDateString())->whereIn('status', ['present', 'late'])->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Late Arrivals</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\Attendance::where('date', now()->toDateString())->where('status', 'late')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Absent</p>
            <p class="text-2xl font-bold text-red-600">0</p> {{-- Placeholder --}}
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Avg. Hours</p>
            <p class="text-2xl font-bold text-indigo-600">{{ round(\App\Models\Attendance::where('date', now()->toDateString())->avg('total_hours') ?? 0, 1) }}h</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="relative w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search employee..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div class="flex gap-2">
                <select class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option>All Status</option>
                    <option>Present</option>
                    <option>Late</option>
                    <option>Absent</option>
                </select>
                <input type="date" value="{{ now()->toDateString() }}" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Employee</th>
                        <th class="px-5 py-4 border-b border-gray-100">Department</th>
                        <th class="px-5 py-4 border-b border-gray-100">Check In</th>
                        <th class="px-5 py-4 border-b border-gray-100">Check Out</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total Hours</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendances as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($item->employee->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $item->employee->user->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-medium">{{ $item->employee->employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $item->employee->department->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-900 font-medium">
                                {{ $item->check_in ? $item->check_in->format('H:i') : '—' }}
                                @if($item->lateness_minutes > 0)
                                    <span class="text-[10px] text-red-500 block">Late {{ $item->lateness_minutes }}m</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $item->check_out ? $item->check_out->format('H:i') : '—' }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">
                                {{ $item->total_hours > 0 ? $item->total_hours . 'h' : '—' }}
                                @if($item->overtime_hours > 0)
                                    <span class="text-[10px] text-green-600 block">+{{ $item->overtime_hours }}h OT</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($item->status === 'present') bg-green-100 text-green-700
                                    @elseif($item->status === 'late') bg-yellow-100 text-yellow-700
                                    @elseif($item->status === 'absent') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-pencil-simple text-lg"></i></button>
                                <button class="text-gray-400 hover:text-gray-600 transition p-1.5"><i class="ph ph-dots-three-vertical text-lg"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No attendance records found for today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
    </div>
</x-layouts.erp>

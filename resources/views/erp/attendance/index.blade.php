<x-layouts.erp :title="'Attendance Management'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Attendance Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Monitor and manage employee daily attendance logs.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('attendance.export-csv', request()->query()) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-file-csv"></i> Export CSV
            </a>
            <button onclick="document.getElementById('manualEntryModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Manual Entry
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Present Today</p>
            <p class="text-2xl font-bold text-gray-900">{{ $presentToday }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Late Arrivals</p>
            <p class="text-2xl font-bold text-orange-600">{{ $lateToday }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Absent</p>
            <p class="text-2xl font-bold text-red-600">{{ $absentToday }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Avg. Hours</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $avgHours ? round($avgHours, 1) : 0 }}h</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px]">
                    <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" placeholder="Search employee..." value="{{ request('search') }}" class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <select name="department_id" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="">All Status</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="half_day" {{ request('status') === 'half_day' ? 'selected' : '' }}>Half Day</option>
                    <option value="leave" {{ request('status') === 'leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="holiday" {{ request('status') === 'holiday' ? 'selected' : '' }}>Holiday</option>
                </select>
                <input type="date" name="date" value="{{ request('date', now()->toDateString()) }}" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Filter</button>
                @if(request()->anyFilled(['search', 'department_id', 'status', 'date']))
                    <a href="{{ route('attendance.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
                @endif
            </form>
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
                        <th class="px-5 py-4 border-b border-gray-100">Lateness</th>
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
                                        {{ strtoupper(substr($item->employee?->user?->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $item->employee?->user?->name ?? 'Unknown' }}</p>
                                        <p class="text-[10px] text-gray-400 font-medium">{{ $item->employee?->employee_code ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $item->employee?->department?->name ?? '—' }}</td>
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
                            <td class="px-5 py-4 text-sm text-gray-600">
                                @if($item->lateness_minutes > 0)
                                    <span class="text-orange-600 font-medium">{{ $item->lateness_minutes }}m</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($item->status === 'present') bg-green-100 text-green-700
                                    @elseif($item->status === 'late') bg-yellow-100 text-yellow-700
                                    @elseif($item->status === 'absent') bg-red-100 text-red-700
                                    @elseif($item->status === 'half_day') bg-yellow-100 text-yellow-700
                                    @elseif($item->status === 'leave') bg-blue-100 text-blue-700
                                    @elseif($item->status === 'holiday') bg-purple-100 text-purple-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button data-id="{{ $item->id }}" data-check-in="{{ $item->check_in?->format('H:i') ?? '' }}" data-check-out="{{ $item->check_out?->format('H:i') ?? '' }}" data-status="{{ $item->status }}" data-note="{{ $item->note ?? '' }}" onclick="openEditModal(this)" class="text-gray-400 hover:text-indigo-600 transition p-1.5">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">No attendance records found for this date.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
    </div>

    {{-- Manual Entry Modal --}}
    <div id="manualEntryModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Manual Attendance Entry</h3>
                <button onclick="document.getElementById('manualEntryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-xl"></i></button>
            </div>
            <form action="{{ route('attendance.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
                    <select name="employee_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                        <option value="">Select Employee</option>
                        @foreach(\App\Models\Employee::with('user')->get() as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->user?->name }} ({{ $emp->employee_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="date" required value="{{ now()->toDateString() }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check In</label>
                        <input type="time" name="check_in" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check Out</label>
                        <input type="time" name="check_out" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="half_day">Half Day</option>
                        <option value="leave">On Leave</option>
                        <option value="holiday">Holiday</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                    <textarea name="note" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('manualEntryModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Edit Attendance</h3>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-xl"></i></button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check In</label>
                        <input type="time" name="check_in" id="edit_check_in" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check Out</label>
                        <input type="time" name="check_out" id="edit_check_out" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" id="edit_status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="half_day">Half Day</option>
                        <option value="leave">On Leave</option>
                        <option value="holiday">Holiday</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                    <textarea name="note" id="edit_note" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditModal(btn) {
            document.getElementById('editForm').action = '/attendance/' + btn.dataset.id;
            document.getElementById('edit_check_in').value = btn.dataset.checkIn;
            document.getElementById('edit_check_out').value = btn.dataset.checkOut;
            document.getElementById('edit_status').value = btn.dataset.status;
            document.getElementById('edit_note').value = btn.dataset.note;
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
    @endpush
</x-layouts.erp>

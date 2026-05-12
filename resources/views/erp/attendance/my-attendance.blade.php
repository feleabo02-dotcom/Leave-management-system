<x-layouts.erp :title="'My Attendance'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Attendance</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track your daily work hours and check-in status.</p>
        </div>
        <div class="text-right">
            <p class="text-lg font-bold text-gray-900" id="current-time">{{ now()->format('H:i:s') }}</p>
            <p class="text-xs text-gray-500">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Check-in/Out Card --}}
        <div class="md:col-span-1">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm text-center">
                <div class="w-20 h-20 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                    <i class="ph ph-clock text-4xl"></i>
                </div>
                
                @if(!$todayAttendance || !$todayAttendance->check_in)
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Ready to work?</h3>
                    <p class="text-sm text-gray-500 mb-6">You haven't checked in for today yet.</p>
                    <form action="{{ route('attendance.check-in') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center justify-center gap-2">
                            <i class="ph ph-sign-in text-xl"></i> Check In Now
                        </button>
                    </form>
                @elseif(!$todayAttendance->check_out)
                    <h3 class="text-xl font-bold text-gray-900 mb-1">On the clock</h3>
                    <p class="text-sm text-gray-500 mb-1">Checked in at <span class="font-bold text-indigo-600">{{ $todayAttendance->check_in->format('H:i') }}</span></p>
                    <p class="text-xs text-gray-400 mb-6">Current duration: {{ now()->diff($todayAttendance->check_in)->format('%Hh %Im') }}</p>
                    <form action="{{ route('attendance.check-out') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-200 flex items-center justify-center gap-2">
                            <i class="ph ph-sign-out text-xl"></i> Check Out
                        </button>
                    </form>
                @else
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Shift Completed</h3>
                    <p class="text-sm text-gray-500 mb-6">You've completed your work for today.</p>
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Total Hours</p>
                            <p class="text-lg font-bold text-gray-900">{{ $todayAttendance->total_hours }}h</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Status</p>
                            <span class="px-2 py-0.5 text-xs font-bold bg-green-100 text-green-700 rounded-full capitalize">{{ $todayAttendance->status }}</span>
                        </div>
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t border-gray-100 text-left">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Your Shift</h4>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Schedule</span>
                        <span class="font-medium text-gray-900">{{ $employee->shift ? $employee->shift->name : 'No shift assigned' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-1">
                        <span class="text-gray-600">Hours</span>
                        <span class="font-medium text-gray-900">
                            {{ $employee->shift ? \Carbon\Carbon::parse($employee->shift->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($employee->shift->end_time)->format('H:i') : '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- History Table --}}
        <div class="md:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Recent Attendance</h3>
                    <a href="#" class="text-xs text-indigo-600 hover:underline">View Monthly Report</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Date</th>
                                <th class="px-5 py-3 border-b border-gray-100">Check In</th>
                                <th class="px-5 py-3 border-b border-gray-100">Check Out</th>
                                <th class="px-5 py-3 border-b border-gray-100">Work Hours</th>
                                <th class="px-5 py-3 border-b border-gray-100">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($history as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm text-gray-900 font-medium">{{ $item->date->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $item->check_in ? $item->check_in->format('H:i') : '—' }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $item->check_out ? $item->check_out->format('H:i') : '—' }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-900 font-bold">
                                        @if($item->total_hours > 0)
                                            {{ $item->total_hours }}h
                                            @if($item->overtime_hours > 0)
                                                <span class="text-[10px] text-green-600 font-normal ml-1">(+{{ $item->overtime_hours }}h OT)</span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                            @if($item->status === 'present') bg-green-100 text-green-700
                                            @elseif($item->status === 'late') bg-yellow-100 text-yellow-700
                                            @elseif($item->status === 'absent') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No attendance records found for this week.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        setInterval(() => {
            document.getElementById('current-time').innerText = new Date().toLocaleTimeString();
        }, 1000);
    </script>
    @endpush
</x-layouts.erp>

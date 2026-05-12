<x-layouts.erp :title="'My Attendance'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Attendance</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track your daily work hours and attendance history.</p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-bold text-gray-900" id="current-time">{{ now()->format('H:i:s') }}</p>
            <p class="text-xs text-gray-500">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>

    {{-- Monthly Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-green-600">{{ $monthlyStats['present'] }}</p>
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mt-1">Present</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-red-600">{{ $monthlyStats['absent'] }}</p>
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mt-1">Absent</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-orange-600">{{ $monthlyStats['late'] }}</p>
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mt-1">Late</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-indigo-600">{{ $monthlyStats['total_hours'] }}h</p>
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mt-1">Total Hours</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-purple-600">{{ $monthlyStats['overtime_hours'] }}h</p>
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mt-1">Overtime</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Check-in/Out Card --}}
        <div>
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
                    <p class="text-xs text-gray-400 mb-6">Duration: <span id="current-duration">{{ now()->diff($todayAttendance->check_in)->format('%Hh %Im') }}</span></p>
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
                        <span class="font-medium text-gray-900">{{ $employee?->shift ? $employee->shift->name : 'No shift assigned' }}</span>
                    </div>
                    @if($employee?->shift)
                        <div class="flex items-center justify-between text-sm mt-1">
                            <span class="text-gray-600">Hours</span>
                            <span class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($employee?->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($employee?->shift->end_time)->format('H:i') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Monthly Calendar View --}}
        <div class="lg:col-span-3">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">{{ Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</h3>
                    <div class="flex gap-1">
                        <a href="{{ route('attendance.my', ['month' => Carbon\Carbon::create($year, $month, 1)->subMonth()->month, 'year' => Carbon\Carbon::create($year, $month, 1)->subMonth()->year]) }}" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 bg-white text-gray-600">
                            <i class="ph ph-caret-left"></i>
                        </a>
                        <a href="{{ route('attendance.my', ['month' => now()->month, 'year' => now()->year]) }}" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 bg-white text-gray-600 text-xs font-medium px-3">Today</a>
                        <a href="{{ route('attendance.my', ['month' => Carbon\Carbon::create($year, $month, 1)->addMonth()->month, 'year' => Carbon\Carbon::create($year, $month, 1)->addMonth()->year]) }}" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 bg-white text-gray-600">
                            <i class="ph ph-caret-right"></i>
                        </a>
                    </div>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-7 text-center text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-2">
                        <div class="py-1">Mon</div>
                        <div class="py-1">Tue</div>
                        <div class="py-1">Wed</div>
                        <div class="py-1">Thu</div>
                        <div class="py-1">Fri</div>
                        <div class="py-1">Sat</div>
                        <div class="py-1">Sun</div>
                    </div>
                    <div class="grid grid-cols-7 text-center gap-1">
                        @php
                            $firstDay = Carbon\Carbon::create($year, $month, 1);
                            $start = $firstDay->copy()->startOfMonth()->startOfWeek(Carbon\Carbon::MONDAY);
                            $end = $firstDay->copy()->endOfMonth()->endOfWeek(Carbon\Carbon::SUNDAY);
                            $todayStr = now()->format('Y-m-d');
                        @endphp
                        @for($date = $start; $date->lte($end); $date->addDay())
                            @php
                                $dateStr = $date->format('Y-m-d');
                                $record = $monthlyRecords->get($dateStr);
                                $isToday = $dateStr === $todayStr;
                                $isCurrentMonth = $date->month === $month;
                            @endphp
                            <div class="aspect-square rounded-lg flex flex-col items-center justify-center text-xs font-medium border border-transparent
                                @if(!$isCurrentMonth) opacity-30
                                @elseif($isToday) border-indigo-400 bg-indigo-50
                                @else bg-gray-50
                                @endif
                            ">
                                <span class="text-gray-700">{{ $date->day }}</span>
                                @if($record)
                                    @if($record->status === 'present')
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mt-1"></span>
                                    @elseif($record->status === 'late')
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mt-1"></span>
                                    @elseif($record->status === 'absent')
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1"></span>
                                    @elseif($record->status === 'half_day')
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mt-1"></span>
                                    @elseif($record->status === 'leave')
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1"></span>
                                    @elseif($record->status === 'holiday')
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 mt-1"></span>
                                    @endif
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- History Table --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Recent Attendance</h3>
                    <a href="{{ route('attendance.my', ['month' => now()->month, 'year' => now()->year]) }}" class="text-xs text-indigo-600 hover:underline">View Monthly Report</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Date</th>
                                <th class="px-5 py-3 border-b border-gray-100">Check In</th>
                                <th class="px-5 py-3 border-b border-gray-100">Check Out</th>
                                <th class="px-5 py-3 border-b border-gray-100">Work Hours</th>
                                <th class="px-5 py-3 border-b border-gray-100">Lateness</th>
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
                                    <td class="px-5 py-3 text-sm text-gray-600">
                                        @if($item->lateness_minutes > 0)
                                            <span class="text-orange-600 font-medium">{{ $item->lateness_minutes }}m</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No attendance records found.</td>
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
            const now = new Date();
            document.getElementById('current-time').innerText = now.toLocaleTimeString();
            const el = document.getElementById('current-duration');
            if (el) {
                const parts = el.innerText.match(/(\d+)h\s*(\d+)m/);
                if (parts) {
                    let m = parseInt(parts[1]) * 60 + parseInt(parts[2]) + 1;
                    el.innerText = Math.floor(m / 60) + 'h ' + (m % 60) + 'm';
                }
            }
        }, 60000);
    </script>
    @endpush
</x-layouts.erp>

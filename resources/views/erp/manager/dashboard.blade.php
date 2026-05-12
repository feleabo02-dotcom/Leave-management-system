<x-layouts.erp :title="'Team Leadership Dashboard'">
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Manager Welcome Header --}}
    <div class="flex items-center justify-between mb-8 bg-slate-900 rounded-xl p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="relative z-10">
            <h1 class="text-3xl font-black tracking-tight mb-2">Team Overview</h1>
            <p class="text-slate-400 text-sm font-medium">Managing {{ $teamCount }} direct reports across {{ auth()->user()->department?->name ?? 'Primary' }} department.</p>
        </div>
        <div class="flex items-center gap-3 relative z-10">
            <div class="px-4 py-2 bg-indigo-600/20 border border-indigo-500/30 rounded-lg text-xs font-bold text-indigo-400">
                {{ $pendingApprovals }} PENDING APPROVALS
            </div>
        </div>
        <i data-lucide="shield-check" class="absolute -right-8 -bottom-8 w-64 h-64 text-white/5 -rotate-12"></i>
    </div>

    {{-- Quick Insights --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Team Presence</span>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ $teamAttendanceToday }} / {{ $teamCount }}</p>
            <p class="text-[10px] text-slate-500 font-medium mt-2">Check-ins recorded today</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-amber-50 rounded-lg">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Pending Actions</span>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ $pendingApprovals }}</p>
            <p class="text-[10px] text-amber-600 font-bold mt-2">Immediate Review Required</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <i data-lucide="activity" class="w-4 h-4 text-indigo-600"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Team Capacity</span>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ round(($availableToday / max($teamCount, 1)) * 100) }}%</p>
            <p class="text-[10px] text-slate-500 font-medium mt-2">Active Workforce Level</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        {{-- Team Capacity (Doughnut) --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Workforce Allocation</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Real-time</span>
            </div>
            <div class="p-6 h-[300px]">
                <canvas id="teamCapacityDonut"></canvas>
            </div>
        </div>

        {{-- Team Productivity (Bar Chart) --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Output Velocity</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tasks Completed</span>
            </div>
            <div class="p-6 h-[300px]">
                <canvas id="teamProductivityChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Pending Approvals Table --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Pending Leave Approvals</h3>
            <a href="{{ route('manager.dashboard') }}" class="text-xs font-bold text-indigo-600 hover:underline">Approval Center</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Period</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentLeaves as $leave)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                        {{ strtoupper(substr($leave->user->name, 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-900">{{ $leave->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase rounded">
                                    {{ $leave->leaveType->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-600">
                                {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="p-1.5 bg-emerald-50 text-emerald-600 rounded hover:bg-emerald-100 transition shadow-sm border border-emerald-100">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-1.5 bg-rose-50 text-rose-600 rounded hover:bg-rose-100 transition shadow-sm border border-rose-100">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic text-sm">No pending actions.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            // Team Capacity Donut
            new Chart(document.getElementById('teamCapacityDonut'), {
                type: 'doughnut',
                data: {
                    labels: ['Available', 'On Leave'],
                    datasets: [{
                        data: [{{ $availableToday }}, {{ $onLeaveToday }}],
                        backgroundColor: ['#4f46e5', '#f1f5f9'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 10, weight: 'bold' }, usePointStyle: true } }
                    }
                }
            });

            // Team Productivity Chart (Bar)
            new Chart(document.getElementById('teamProductivityChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($weekDays) !!},
                    datasets: [{
                        label: 'Tasks Completed',
                        data: {!! json_encode($chartTasks) !!},
                        backgroundColor: '#4f46e5',
                        borderRadius: 4,
                        barThickness: 32
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</x-layouts.erp>

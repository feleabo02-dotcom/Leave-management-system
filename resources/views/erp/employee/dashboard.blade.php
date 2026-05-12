<x-layouts.erp :title="'Employee Workspace'">
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Welcome Header --}}
    <div class="flex items-center justify-between mb-8 bg-slate-900 rounded-xl p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="relative z-10">
            <h1 class="text-3xl font-black tracking-tight mb-2">Hello, {{ auth()->user()->name }}</h1>
            <p class="text-slate-400 text-sm font-medium">You have {{ $balances->sum('remaining') }} total leave days available for {{ $year }}.</p>
        </div>
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('leave-requests.create') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-black uppercase tracking-widest transition shadow-lg shadow-indigo-900/40">
                Request Leave
            </a>
        </div>
        <i data-lucide="sparkles" class="absolute -right-8 -bottom-8 w-64 h-64 text-white/5 -rotate-12"></i>
    </div>

    {{-- Leave Balance Usage (Circle Bars) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($balances as $balance)
            @php 
                $pct = $balance->allocated > 0 ? min(100, ($balance->used / $balance->allocated) * 100) : 0;
            @endphp
            <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $balance->type->name }}</span>
                    <i data-lucide="pie-chart" class="w-4 h-4 text-indigo-400"></i>
                </div>
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 relative">
                        <canvas id="usageDonut_{{ $balance->type->id }}"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-[10px] font-black text-slate-900">{{ round($pct) }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-900">{{ number_format($balance->remaining, 1) }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Days Left</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        {{-- Attendance Productivity (Weekly Bar) --}}
        <div class="xl:col-span-2 bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Weekly Presence</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase">This Week</span>
            </div>
            <div class="p-6 h-[300px]">
                <canvas id="weeklyAttendanceChart"></canvas>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="space-y-6">
            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-6">
                <h3 class="text-indigo-900 font-black text-xs uppercase tracking-widest mb-4">My Utilization</h3>
                <div class="space-y-4">
                    @foreach($balances as $balance)
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-[10px] font-bold uppercase tracking-tight text-indigo-700">
                                <span>{{ $balance->type->name }}</span>
                                <span>{{ $balance->used }} / {{ $balance->allocated }}</span>
                            </div>
                            <div class="w-full h-1 bg-indigo-200 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $balance->allocated > 0 ? ($balance->used / $balance->allocated) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
                <h3 class="font-bold text-xs uppercase tracking-widest text-slate-400 mb-4">Next Holiday</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="calendar" class="w-6 h-6 text-slate-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">National Day</p>
                        <p class="text-xs text-slate-400 font-medium">In 14 days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Request History --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">My Recent Requests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        <th class="px-6 py-4">Leave Type</th>
                        <th class="px-6 py-4">Duration</th>
                        <th class="px-6 py-4">Days</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentRequests as $req)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-sm font-bold text-slate-900">{{ $req->leaveType->name }}</td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-600">
                                {{ $req->start_date->format('M d') }} - {{ $req->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-900">{{ $req->days }}d</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest
                                    {{ match($req->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'pending'  => 'bg-amber-100 text-amber-700',
                                        'rejected' => 'bg-rose-100 text-rose-700',
                                        default    => 'bg-slate-100 text-slate-700'
                                    } }}">
                                    {{ $req->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic text-sm">No recent leave requests.</td>
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

            // Weekly Attendance Chart (Bar)
            new Chart(document.getElementById('weeklyAttendanceChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($weekDays) !!},
                    datasets: [{
                        label: 'Check-ins',
                        data: {!! json_encode($chartAttendance) !!},
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

            // Usage Doughnuts (Circle Bars)
            @foreach($balances as $balance)
                @php $pct = $balance->allocated > 0 ? min(100, ($balance->used / $balance->allocated) * 100) : 0; @endphp
                new Chart(document.getElementById('usageDonut_{{ $balance->type->id }}'), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [{{ $pct }}, {{ 100 - $pct }}],
                            backgroundColor: ['#4f46e5', '#f1f5f9'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '80%',
                        plugins: { tooltip: { enabled: false } }
                    }
                });
            @endforeach
        });
    </script>
</x-layouts.erp>

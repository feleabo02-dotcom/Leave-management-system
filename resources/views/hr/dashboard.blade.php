<x-layouts.erp :title="'HR Intelligence'">
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- HR Summary Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <i data-lucide="users-2" class="w-4 h-4 text-indigo-600"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Headcount</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($totalEmployees) }}</p>
            <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                <span>{{ $newHiresThisYear }} this year</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($activeEmployees) }}</p>
            <p class="text-[10px] text-slate-500 font-medium mt-2">Utilization: {{ round(($activeEmployees / max($totalEmployees, 1)) * 100) }}%</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-amber-50 rounded-lg">
                    <i data-lucide="clock-4" class="w-4 h-4 text-amber-600"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Probation</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $probationEmployees }}</p>
            <p class="text-[10px] text-slate-500 font-medium mt-2">Pending Review</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-rose-50 rounded-lg">
                    <i data-lucide="user-minus" class="w-4 h-4 text-rose-600"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Churn</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $terminatedEmployees }}</p>
            <p class="text-[10px] text-rose-500 font-bold mt-2">2.4% Annual Rate</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        {{-- Hiring Trend (Area) --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Acquisition Velocity</h3>
            </div>
            <div class="p-6 h-[350px]">
                <canvas id="hiringTrendChart"></canvas>
            </div>
        </div>

        {{-- Department Distribution (Doughnut) --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Force Distribution</h3>
            </div>
            <div class="p-6 h-[350px]">
                <canvas id="deptDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Talent Acquisition Feed --}}
        <div class="xl:col-span-2 bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">New Onboardings</h3>
                <a href="{{ route('employees.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Full Directory</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            <th class="px-6 py-4">Professional</th>
                            <th class="px-6 py-4">Position</th>
                            <th class="px-6 py-4">Department</th>
                            <th class="px-6 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentEmployees as $emp)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                            {{ strtoupper(substr($emp->user->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-900">{{ $emp->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-600">{{ $emp->position?->title ?? 'Executive' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase rounded">
                                        {{ $emp->department?->name ?? 'Corporate' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 font-medium">{{ $emp->hire_date->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Attendance Pie --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Daily Presence</h3>
            </div>
            <div class="p-6 h-[300px]">
                <canvas id="presenceChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            // Hiring Trend (Area Chart)
            new Chart(document.getElementById('hiringTrendChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        label: 'New Hires',
                        data: {!! json_encode($hiringCounts) !!},
                        fill: true,
                        backgroundColor: 'rgba(79, 70, 229, 0.05)',
                        borderColor: '#4f46e5',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Department Distribution (Doughnut)
            new Chart(document.getElementById('deptDistributionChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($departmentCounts->map(fn($d) => $d->department?->name ?? 'Other')) !!},
                    datasets: [{
                        data: {!! json_encode($departmentCounts->pluck('total')) !!},
                        backgroundColor: ['#4f46e5', '#818cf8', '#c7d2fe', '#e2e8f0', '#f1f5f9'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 10, weight: 'bold' }, usePointStyle: true } }
                    }
                }
            });

            // Presence Pie
            new Chart(document.getElementById('presenceChart'), {
                type: 'pie',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{
                        data: [{{ $todayAttendance['present'] }}, {{ $todayAttendance['absent'] }}],
                        backgroundColor: ['#10b981', '#f1f5f9'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 10, weight: 'bold' }, usePointStyle: true } }
                    }
                }
            });
        });
    </script>
</x-layouts.erp>

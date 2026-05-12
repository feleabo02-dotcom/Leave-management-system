<x-layouts.erp :title="'Admin Dashboard'">
    {{-- Include Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Summary Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Attendance Card --}}
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Live</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($currentAttendance) }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Current Attendance</p>
        </div>

        {{-- Active Tasks --}}
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <i data-lucide="check-square" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Operational</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($activeTasks) }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Active Tasks</p>
        </div>

        {{-- Monthly Revenue --}}
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded text-xs">MTD</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">${{ number_format($monthlyRevenue, 0) }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Monthly Revenue</p>
        </div>

        {{-- Inventory Alerts --}}
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-rose-50 rounded-lg">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
                </div>
                @if($inventoryAlerts > 0)
                    <span class="flex h-2 w-2 rounded-full bg-rose-500 animate-ping"></span>
                @endif
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $inventoryAlerts }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Inventory Alerts</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        {{-- Large Section: Sales vs Expenses --}}
        <div class="xl:col-span-2 bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Performance Dynamics</h3>
                <div class="flex items-center gap-4 text-xs font-bold">
                    <div class="flex items-center gap-1.5 text-indigo-600">
                        <span class="w-3 h-3 rounded-sm bg-indigo-600"></span> Sales
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <span class="w-3 h-3 rounded-sm bg-slate-200"></span> Expenses
                    </div>
                </div>
            </div>
            <div class="p-6 h-[400px]">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        {{-- NEW: Revenue Distribution (Circle Bar/Doughnut) --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Revenue Sources</h3>
            </div>
            <div class="p-6 flex flex-col items-center justify-center h-[400px]">
                <div class="w-full h-full relative">
                    <canvas id="revenueDonut"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        {{-- Quick Actions --}}
        <div class="bg-slate-900 rounded-lg p-6 shadow-xl text-white relative overflow-hidden">
            <h3 class="font-bold text-xs uppercase tracking-widest text-slate-400 mb-6">Strategic Controls</h3>
            <div class="grid grid-cols-1 gap-3 relative z-10">
                <a href="{{ route('employees.create') }}" class="flex items-center gap-3 w-full p-3 bg-white/5 hover:bg-white/10 rounded-lg transition text-sm font-medium border border-white/10">
                    <i data-lucide="user-plus" class="w-4 h-4 text-indigo-400"></i>
                    Provision User
                </a>
                <button class="flex items-center gap-3 w-full p-3 bg-white/5 hover:bg-white/10 rounded-lg transition text-sm font-medium border border-white/10 text-left">
                    <i data-lucide="file-text" class="w-4 h-4 text-emerald-400"></i>
                    Issue Statement
                </button>
                <button class="flex items-center gap-3 w-full p-3 bg-white/5 hover:bg-white/10 rounded-lg transition text-sm font-medium border border-white/10 text-left">
                    <i data-lucide="package-plus" class="w-4 h-4 text-amber-400"></i>
                    Stock Catalog
                </button>
            </div>
            <i data-lucide="settings" class="absolute -right-8 -bottom-8 w-48 h-48 text-white/5 rotate-12"></i>
        </div>

        {{-- Operational Load (Vertical Bar) --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Operational Load</h3>
            </div>
            <div class="p-6 h-[200px]">
                <canvas id="projectStatusChart"></canvas>
            </div>
        </div>

        {{-- Pending Approvals (New) --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider mb-1">Attention Required</h3>
                <p class="text-xs text-slate-400 font-medium">Pending Manager Approvals</p>
            </div>
            <div class="flex items-end justify-between mt-4">
                <div class="flex flex-col">
                    <span class="text-3xl font-black text-rose-600">12</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Requests</span>
                </div>
                <a href="{{ route('manager.dashboard') }}" class="px-3 py-1.5 bg-slate-100 rounded-lg text-[10px] font-black text-slate-600 hover:bg-indigo-600 hover:text-white transition uppercase tracking-widest">
                    Review
                </a>
            </div>
        </div>
    </div>

    {{-- Live Attendance & Activity Feed --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        {{-- Attendance Table --}}
        <div class="xl:col-span-2 bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Live Attendance Feed</h3>
                <a href="{{ route('attendance.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">View Log</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Check-In Time</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($lastCheckIns as $log)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                            {{ strtoupper(substr($log->employee->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $log->employee->user->name }}</p>
                                            <p class="text-[10px] text-slate-500 font-medium">ID: EMP-{{ $log->employee->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 font-medium">{{ \Carbon\Carbon::parse($log->check_in)->format('H:i:s') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $log->status_color }}-100 text-{{ $log->status_color }}-700">
                                        {{ $log->status_badge }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400 text-sm italic font-medium">No activity today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Activity Feed (Odoo Style) --}}
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Enterprise Log</h3>
            </div>
            <div class="flex-1 p-6 space-y-6 overflow-y-auto max-h-[500px] custom-scrollbar">
                @forelse($recentActivities as $activity)
                    <div class="flex gap-4 relative group">
                        {{-- Timeline line --}}
                        @if(!$loop->last)
                            <div class="absolute left-4 top-8 bottom-[-24px] w-0.5 bg-slate-100 group-hover:bg-indigo-100 transition"></div>
                        @endif
                        
                        <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 flex-shrink-0 flex items-center justify-center text-indigo-600 z-10">
                            <i class="ph ph-lightning text-sm"></i>
                        </div>
                        <div class="flex-1 pt-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-bold text-slate-900 leading-tight">{{ $activity->action }}</p>
                                <span class="text-[9px] font-medium text-slate-400 whitespace-nowrap">{{ $activity->created_at->diffForHumans(null, true) }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">
                                By <span class="font-bold text-indigo-600">{{ $activity->user->name ?? 'System' }}</span>
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center p-8">
                        <i class="ph ph-notebook text-4xl text-slate-100 mb-2"></i>
                        <p class="text-xs font-bold text-slate-400 italic">No logs found.</p>
                    </div>
                @endforelse
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                <button class="w-full py-2 text-[10px] font-black text-slate-400 hover:text-indigo-600 uppercase tracking-widest transition">
                    View Complete Audit Trail
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide
            lucide.createIcons();

            // Performance Bar Chart
            const ctx = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [
                        {
                            label: 'Sales',
                            data: {!! json_encode($chartSales) !!},
                            backgroundColor: '#4f46e5',
                            borderRadius: 4,
                            barThickness: 32
                        },
                        {
                            label: 'Expenses',
                            data: {!! json_encode($chartExpenses) !!},
                            backgroundColor: '#e2e8f0',
                            borderRadius: 4,
                            barThickness: 32
                        }
                    ]
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

            // NEW: Revenue Donut (Circle Bar)
            const donutCtx = document.getElementById('revenueDonut').getContext('2d');
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($revenueByCategory->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($revenueByCategory->pluck('total')) !!},
                        backgroundColor: ['#4f46e5', '#818cf8', '#c7d2fe', '#e2e8f0', '#f1f5f9'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: { size: 10, weight: 'bold' },
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            // Project Status Chart (Vertical Bar)
            const statusCtx = document.getElementById('projectStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($projectStatusCounts->pluck('status')) !!},
                    datasets: [{
                        label: 'Projects',
                        data: {!! json_encode($projectStatusCounts->pluck('count')) !!},
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barThickness: 24
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false } }
                    }
                }
            });
        });
    </script>
</x-layouts.erp>

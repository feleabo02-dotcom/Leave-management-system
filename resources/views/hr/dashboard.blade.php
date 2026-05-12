<x-layouts.erp :title="'HR Dashboard'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">HR Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Workforce overview — {{ now()->format('F Y') }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employees.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Hire
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-3 hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center">
                <i class="ph ph-users text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalEmployees) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Employees</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-3 hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-green-50 text-green-700 border border-green-100 flex items-center justify-center">
                <i class="ph ph-user-check text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-700">{{ number_format($activeEmployees) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Active</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-3 hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-100 flex items-center justify-center">
                <i class="ph ph-clock text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-700">{{ number_format($probationEmployees) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Probation</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-3 hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center">
                <i class="ph ph-user-plus text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-blue-700">{{ number_format($newHires) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">New Hires (Month)</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Department Breakdown --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ph ph-buildings text-indigo-500"></i> Headcount by Department
                </h2>
            </div>
            @if($departmentCounts->isNotEmpty())
                @php $maxDept = $departmentCounts->max('total') ?: 1; @endphp
                <div class="p-5 space-y-4">
                    @foreach($departmentCounts as $dept)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-700 font-medium">{{ $dept->department?->name ?? 'Unknown' }}</span>
                                <span class="text-gray-500">{{ $dept->total }}</span>
                            </div>
                            <div class="bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" style="width: {{ ($dept->total / $maxDept) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-6 text-center text-sm text-gray-400">No departments configured.</div>
            @endif
        </div>

        {{-- Recent Hires + Stats --}}
        <div class="xl:col-span-2 flex flex-col gap-6">
            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-700 border border-red-100 flex items-center justify-center">
                            <i class="ph ph-user-minus text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($terminatedEmployees) }}</p>
                            <p class="text-xs text-gray-500">Terminated</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-700 border border-teal-100 flex items-center justify-center">
                            <i class="ph ph-calendar-plus text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($newHiresThisYear) }}</p>
                            <p class="text-xs text-gray-500">New Hires (Year)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Employees --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden flex-1">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-user-list text-indigo-500"></i> Recent Employees
                    </h2>
                    <a href="{{ route('employees.index') }}" class="text-xs text-indigo-600 hover:underline">View all →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentEmployees as $emp)
                        <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($emp->user?->name ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $emp->user?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $emp->position?->title ?? 'No Position' }} &bull; {{ $emp->department?->name ?? 'No Dept' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('employees.show', $emp) }}" class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No employees yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>

<x-layouts.erp :title="'Dashboard'">

{{-- ─── Page Header ─────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            Welcome back, <span class="font-medium text-indigo-600">{{ auth()->user()->name }}</span> —
            {{ now()->format('l, F j, Y') }}
        </p>
    </div>
</div>

{{-- ─── KPI Cards ───────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Total Employees', 'value' => $stats['total_employees'],   'icon' => 'ph-users',          'color' => 'indigo'],
            ['label' => 'Active',           'value' => $stats['active_employees'], 'icon' => 'ph-user-check',     'color' => 'green'],
            ['label' => 'Departments',      'value' => $stats['departments'],      'icon' => 'ph-building',        'color' => 'blue'],
            ['label' => 'Pending Leaves',   'value' => $stats['pending_leaves'],   'icon' => 'ph-calendar-x',     'color' => 'yellow'],
            ['label' => 'Approved (Month)', 'value' => $stats['approved_leaves'],  'icon' => 'ph-calendar-check', 'color' => 'green'],
            ['label' => 'My Pending',       'value' => $stats['my_pending_leaves'],'icon' => 'ph-clock-countdown','color' => 'orange'],
        ];
        $colors = [
            'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
            'green'  => 'bg-green-50  text-green-700  border-green-100',
            'blue'   => 'bg-blue-50   text-blue-700   border-blue-100',
            'yellow' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
            'orange' => 'bg-orange-50 text-orange-700 border-orange-100',
        ];
    @endphp

    @foreach($cards as $card)
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-3 hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg {{ $colors[$card['color']] }} flex items-center justify-center border">
                <i class="ph {{ $card['icon'] }} text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($card['value']) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- ─── Content Grid ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Pending Approvals --}}
    <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ph ph-hourglass text-indigo-500"></i> Pending Leave Approvals
            </h2>
            @if(\Illuminate\Support\Facades\Route::has('leave-requests.index'))
                <a href="{{ route('leave-requests.index', ['status' => 'submitted']) }}"
                   class="text-xs text-indigo-600 hover:underline">View all →</a>
            @endif
        </div>

        @if($pendingApprovals->isEmpty())
            <div class="px-5 py-12 text-center text-gray-400">
                <i class="ph ph-check-circle text-4xl mb-2 text-green-400"></i>
                <p class="text-sm">No pending approvals</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($pendingApprovals as $req)
                    <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($req->user?->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $req->user?->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $req->leaveType?->name }} ·
                                {{ $req->start_date?->format('M d') }} – {{ $req->end_date?->format('M d, Y') }}
                            </p>
                        </div>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">Pending</span>
                        @if(\Illuminate\Support\Facades\Route::has('leave-requests.show'))
                            <a href="{{ route('leave-requests.show', $req->id) }}"
                               class="text-xs text-indigo-600 hover:underline font-medium">Review</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Quick Actions + Recent Activity --}}
    <div class="flex flex-col gap-6">
        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ph ph-lightning text-yellow-500"></i> Quick Actions
            </h2>
            <div class="grid grid-cols-2 gap-2">
                @php
                    $actions = [
                        ['label' => 'New Employee',   'icon' => 'ph-user-plus',      'route' => 'employees.create',     'color' => 'indigo'],
                        ['label' => 'Apply Leave',    'icon' => 'ph-calendar-plus',   'route' => 'leave-requests.create','color' => 'blue'],
                        ['label' => 'Add Asset',      'icon' => 'ph-plus-circle',     'route' => 'assets.create',        'color' => 'green'],
                        ['label' => 'New Purchase',   'icon' => 'ph-shopping-bag',    'route' => 'procurement.create',   'color' => 'purple'],
                    ];
                    $qColors = [
                        'indigo' => 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
                        'blue'   => 'bg-blue-50   text-blue-700   hover:bg-blue-100',
                        'green'  => 'bg-green-50  text-green-700  hover:bg-green-100',
                        'purple' => 'bg-purple-50 text-purple-700 hover:bg-purple-100',
                    ];
                @endphp
                @foreach($actions as $action)
                    @if(\Illuminate\Support\Facades\Route::has($action['route']))
                        <a href="{{ route($action['route']) }}"
                           class="flex flex-col items-center gap-1.5 p-3 rounded-xl text-center text-xs font-medium transition {{ $qColors[$action['color']] }}">
                            <i class="ph {{ $action['icon'] }} text-xl"></i>
                            {{ $action['label'] }}
                        </a>
                    @else
                        <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl text-center text-xs font-medium bg-gray-50 text-gray-400 cursor-not-allowed">
                            <i class="ph {{ $action['icon'] }} text-xl"></i>
                            {{ $action['label'] }}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden flex-1">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ph ph-activity text-indigo-500"></i> Recent Activity
                </h2>
            </div>
            <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                @forelse($recentActivity as $log)
                    <div class="flex items-start gap-3 px-5 py-3 hover:bg-gray-50 transition">
                        <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs flex-shrink-0 mt-0.5">
                            <i class="ph ph-activity"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-700 truncate">
                                <span class="font-medium">{{ $log->user?->name ?? 'System' }}</span>
                                {{ $log->event }}
                                @if($log->auditable_type)
                                    <span class="text-gray-400">{{ class_basename($log->auditable_type) }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400">{{ $log->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-6 text-center text-sm text-gray-400">No activity yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ─── Leave by Department Chart ───────────────────────────────────────── --}}
@if($leaveByDept->isNotEmpty())
<div class="mt-6 bg-white rounded-xl border border-gray-200 p-5">
    <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="ph ph-chart-bar text-indigo-500"></i> Leave by Department (This Year)
    </h2>
    <div class="space-y-3">
        @php $max = $leaveByDept->max('total') ?: 1; @endphp
        @foreach($leaveByDept as $row)
            <div class="flex items-center gap-3">
                <span class="w-32 text-xs text-gray-600 truncate flex-shrink-0">{{ $row->dept }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-500"
                         style="width: {{ ($row->total / $max) * 100 }}%"></div>
                </div>
                <span class="text-xs font-semibold text-gray-700 w-6 text-right">{{ $row->total }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

</x-layouts.erp>

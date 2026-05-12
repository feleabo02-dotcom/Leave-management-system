<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'XobiyaHR') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 64px;
        }
        body { font-family: 'Inter', sans-serif; }
        .subnav-enter { opacity: 0; transform: translateY(-4px); }
        .subnav-enter-active { opacity: 1; transform: translateY(0); transition: all 0.15s ease; }
        
        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .sidebar-scroll:hover::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body class="h-full bg-gray-50 text-gray-800" x-data="{ sidebarOpen: true, notifOpen: false, expandedGroups: ['hr', 'supply-chain', 'services', 'gamification', 'operations', 'finance'] }">

{{-- ─── SIDEBAR ──────────────────────────────────────────────────────────────── --}}
<aside
    class="fixed inset-y-0 left-0 z-40 flex flex-col bg-gray-900 text-white transition-all duration-300"
    :class="sidebarOpen ? 'w-64' : 'w-16'"
>
    {{-- Logo acting as Toggle --}}
    <div @click="sidebarOpen = !sidebarOpen" 
         class="flex items-center gap-3 px-4 py-5 border-b border-gray-800 overflow-hidden cursor-pointer hover:bg-gray-800/50 transition-colors group">
        <div class="w-8 h-8 flex-shrink-0 rounded-lg bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-900/20 group-hover:scale-110 transition-transform">
            <i class="ph ph-cube text-white text-lg"></i>
        </div>
        <span x-show="sidebarOpen" x-transition class="font-bold text-lg tracking-tight whitespace-nowrap text-white group-hover:text-indigo-400 transition-colors">
            {{ config('app.name', 'XobiyaHR') }}
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto sidebar-scroll py-4 px-2 space-y-4">
        @php
            function isNavActive($route) {
                return request()->routeIs(str_replace('.index', '.*', $route));
            }

            function routeExists($route) {
                return \Illuminate\Support\Facades\Route::has($route);
            }

            $navigation = [
                'main' => [
                    ['route' => 'dashboard', 'icon' => 'ph-squares-four', 'label' => 'Dashboard', 'perm' => null],
                ],
                'hr' => [
                    'label' => 'Human Resources',
                    'icon' => 'ph-users-three',
                    'perm' => 'employees.read',
                    'children' => [
                        ['route' => 'employees.index',   'icon' => 'ph-identification-card', 'label' => 'Directory'],
                        ['route' => 'hr.dashboard',      'icon' => 'ph-chart-pie',           'label' => 'HR Analytics'],
                        ['route' => 'positions.index',   'icon' => 'ph-briefcase',           'label' => 'Positions'],
                        ['route' => 'attendance.index',  'icon' => 'ph-clock',               'label' => 'Attendance Logs'],
                        ['route' => 'attendance.my',     'icon' => 'ph-user-focus',          'label' => 'My Attendance'],
                        ['route' => 'payroll.index',     'icon' => 'ph-money',               'label' => 'Payroll Admin'],
                        ['route' => 'payroll.runs',      'icon' => 'ph-tray',                'label' => 'Payroll Runs'],
                        ['route' => 'payroll.my',        'icon' => 'ph-file-text',           'label' => 'My Payslips'],
                        ['route' => 'skills.index',       'icon' => 'ph-lightbulb',           'label' => 'Skills'],
                        ['route' => 'skills.employees',   'icon' => 'ph-users-four',          'label' => 'Employee Skills'],
                        ['route' => 'skills.resume-lines','icon' => 'ph-file-text',           'label' => 'Resume Lines'],
                    ]
                ],
                'leave' => [
                    'label' => 'Time Off',
                    'icon' => 'ph-calendar-blank',
                    'perm' => 'leave.read',
                    'children' => [
                        ['route' => 'employee.dashboard',      'icon' => 'ph-calendar',        'label' => 'My Time Off'],
                        ['route' => 'manager.dashboard',       'icon' => 'ph-check-circle',    'label' => 'Approvals'],
                        ['route' => 'admin.leave-types',       'icon' => 'ph-tag',              'label' => 'Leave Types'],
                        ['route' => 'admin.allocations',       'icon' => 'ph-coins',            'label' => 'Allocations'],
                        ['route' => 'admin.accrual-plans',     'icon' => 'ph-clock-counter-clockwise', 'label' => 'Accrual Plans'],
                    ]
                ],
                'supply-chain' => [
                    'label' => 'Supply Chain',
                    'icon' => 'ph-truck',
                    'perm' => 'inventory.read',
                    'children' => [
                        ['route' => 'inventory.index',         'icon' => 'ph-warehouse',          'label' => 'Inventory'],
                        ['route' => 'inventory.lots',       'icon' => 'ph-qr-code',             'label' => 'Lots / Serials'],
                        ['route' => 'inventory.batches',    'icon' => 'ph-stack',               'label' => 'Picking Batches'],
                        ['route' => 'inventory.landed-costs','icon' => 'ph-currency-dollar',    'label' => 'Landed Costs'],
                        ['route' => 'procurement.index',       'icon' => 'ph-shopping-cart',      'label' => 'Procurement'],
                        ['route' => 'procurement.agreements','icon' => 'ph-file-contract',      'label' => 'Purchase Agreements'],
                        ['route' => 'procurement.requisitions','icon' => 'ph-clipboard-text',   'label' => 'Purchase Requisitions'],
                    ]
                ],
                'crm-sales' => [
                    'label' => 'CRM & Sales',
                    'icon' => 'ph-funnel',
                    'perm' => 'crm.read',
                    'children' => [
                        ['route' => 'crm.index',               'icon' => 'ph-users-four',         'label' => 'CRM Pipeline'],
                        ['route' => 'crm.stages',              'icon' => 'ph-layout',             'label' => 'CRM Stages'],
                        ['route' => 'crm.teams',               'icon' => 'ph-users',              'label' => 'CRM Teams'],
                        ['route' => 'sales.index',             'icon' => 'ph-chart-line-up',      'label' => 'Sales Orders'],
                    ]
                ],
                'finance' => [
                    'label' => 'Financials',
                    'icon' => 'ph-bank',
                    'perm' => 'accounting.read',
                    'children' => [
                        ['route' => 'accounting.index',        'icon' => 'ph-calculator',         'label' => 'Dashboard'],
                        ['route' => 'accounting.coa',          'icon' => 'ph-list-numbers',       'label' => 'Chart of Accounts'],
                        ['route' => 'accounting.journals',     'icon' => 'ph-notebook',           'label' => 'Journals'],
                        ['route' => 'accounting.invoices',     'icon' => 'ph-file-invoice',       'label' => 'Invoices'],
                        ['route' => 'accounting.taxes',        'icon' => 'ph-percent',            'label' => 'Taxes'],
                        ['route' => 'assets.index',            'icon' => 'ph-cube',               'label' => 'Asset Mgmt'],
                        ['route' => 'assets.my',               'icon' => 'ph-laptop',             'label' => 'My Assets'],
                        ['route' => 'assets.maintenance',      'icon' => 'ph-wrench',             'label' => 'Maintenance'],
                    ]
                ],
                'projects' => [
                    'label' => 'Projects',
                    'icon' => 'ph-briefcase-metal',
                    'perm' => 'projects.read',
                    'children' => [
                        ['route' => 'projects.index',          'icon' => 'ph-kanban',             'label' => 'Project Boards'],
                    ]
                ],
                'operations' => [
                    'label' => 'Operations',
                    'icon' => 'ph-factory',
                    'perm' => 'manufacturing.read',
                    'children' => [
                        ['route' => 'manufacturing.index',     'icon' => 'ph-factory',            'label' => 'Manufacturing'],
                        ['route' => 'manufacturing.boms',      'icon' => 'ph-list-bullets',       'label' => 'Bill of Materials'],
                        ['route' => 'manufacturing.work-centers','icon' => 'ph-toolbox',          'label' => 'Work Centers'],
                        ['route' => 'manufacturing.routings',  'icon' => 'ph-split-horizontal',   'label' => 'Routings'],
                        ['route' => 'fleet.index',             'icon' => 'ph-car',                'label' => 'Fleet'],
                        ['route' => 'maintenance.index',       'icon' => 'ph-wrench',             'label' => 'Maintenance'],
                        ['route' => 'maintenance.requests',    'icon' => 'ph-clipboard',          'label' => 'Maint. Requests'],
                        ['route' => 'repair.index',            'icon' => 'ph-toolbox',            'label' => 'Repair Orders'],
                    ]
                ],
                'services' => [
                    'label' => 'Services',
                    'icon' => 'ph-headset',
                    'perm' => 'helpdesk.read',
                    'children' => [
                        ['route' => 'helpdesk.index',            'icon' => 'ph-ticket',               'label' => 'Helpdesk'],
                        ['route' => 'expenses.index',            'icon' => 'ph-receipt',              'label' => 'Expenses'],
                        ['route' => 'expenses.my',               'icon' => 'ph-user',                 'label' => 'My Expenses'],
                        ['route' => 'lunch.index',               'icon' => 'ph-coffee',               'label' => 'Lunch'],
                        ['route' => 'lunch.orders',              'icon' => 'ph-shopping-cart',        'label' => 'Lunch Orders'],
                    ]
                ],
                'reports' => [
                    'label' => 'Reports',
                    'icon' => 'ph-chart-bar',
                    'perm' => 'reports.read',
                    'children' => [
                        ['route' => 'reports.index',             'icon' => 'ph-gauge',               'label' => 'Report Center'],
                        ['route' => 'reports.payroll-summary',   'icon' => 'ph-money',               'label' => 'Payroll Summary'],
                        ['route' => 'reports.attendance-summary','icon' => 'ph-clock',               'label' => 'Attendance'],
                        ['route' => 'reports.accounting',        'icon' => 'ph-calculator',           'label' => 'Accounting'],
                    ]
                ],
                'gamification' => [
                    'label' => 'Gamification',
                    'icon' => 'ph-trophy',
                    'perm' => 'gamification.read',
                    'children' => [
                        ['route' => 'gamification.index',        'icon' => 'ph-gauge',                'label' => 'Dashboard'],
                        ['route' => 'gamification.badges',       'icon' => 'ph-seal-check',           'label' => 'Badges'],
                        ['route' => 'gamification.challenges',   'icon' => 'ph-flag-banner',          'label' => 'Challenges'],
                        ['route' => 'gamification.leaderboard',  'icon' => 'ph-ranking',             'label' => 'Leaderboard'],
                    ]
                ],
                'recruitment' => [
                    'label' => 'Recruitment',
                    'icon' => 'ph-magnifying-glass',
                    'perm' => 'recruitment.read',
                    'children' => [
                        ['route' => 'recruitment.index',         'icon' => 'ph-briefcase',            'label' => 'Job Positions'],
                        ['route' => 'recruitment.applications',  'icon' => 'ph-users-four',           'label' => 'Applications'],
                    ]
                ],
                'admin' => [
                    'label' => 'System Control',
                    'icon' => 'ph-gear-six',
                    'perm' => 'users.read',
                    'children' => [
                        ['route' => 'admin.dashboard',         'icon' => 'ph-gauge',            'label' => 'Admin Panel'],
                        ['route' => 'admin.users',             'icon' => 'ph-users-three',      'label' => 'Users & Roles'],
                        ['route' => 'admin.settings',          'icon' => 'ph-sliders',          'label' => 'Settings'],
                        ['route' => 'companies.index',         'icon' => 'ph-buildings',        'label' => 'Companies'],
                    ]
                ]
            ];
        @endphp

        {{-- Main Dashboard Item --}}
        @foreach($navigation['main'] as $item)
            @php $active = isNavActive($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ $active ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/40' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="ph {{ $item['icon'] }} text-xl flex-shrink-0"></i>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">{{ $item['label'] }}</span>
            </a>
        @endforeach

        {{-- Groups --}}
        @foreach($navigation as $key => $group)
            @if($key === 'main') @continue @endif
            @if($group['perm'] === null || auth()->user()?->hasPermission($group['perm']))
                @php
                    $groupActive = collect($group['children'])->contains(fn($c) => isNavActive($c['route']));
                @endphp
                <div x-data="{ open: expandedGroups.includes('{{ $key }}') || {{ $groupActive ? 'true' : 'false' }} }">
                    {{-- Group Header --}}
                    <button @click="open = !open"
                            class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors
                                   {{ $groupActive ? 'text-indigo-400' : 'text-gray-400 hover:text-gray-200' }}">
                        <i class="ph {{ $group['icon'] }} text-base flex-shrink-0"></i>
                        <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">{{ $group['label'] }}</span>
                        <i x-show="sidebarOpen" x-transition
                           class="ph ph-caret-down text-[10px] transition-transform duration-200"
                           :class="open ? 'rotate-0' : '-rotate-90'">
                        </i>
                    </button>

                    {{-- Children --}}
                    <div x-show="sidebarOpen && open" x-collapse
                         class="ml-3.5 mt-1.5 space-y-1 border-l border-gray-800 pl-3">
                        @foreach($group['children'] as $child)
                            @php $childActive = isNavActive($child['route']); @endphp
                            @if(routeExists($child['route']))
                                <a href="{{ route($child['route']) }}"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all
                                          {{ $childActive ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-300 hover:text-gray-100 hover:bg-gray-800' }}">
                                    <i class="ph {{ $child['icon'] }} text-lg flex-shrink-0"></i>
                                    <span x-show="sidebarOpen" class="whitespace-nowrap">{{ $child['label'] }}</span>
                                </a>
                            @else
                                <span class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 cursor-not-allowed">
                                    <i class="ph {{ $child['icon'] }} text-lg flex-shrink-0 opacity-50"></i>
                                    <span x-show="sidebarOpen" class="whitespace-nowrap opacity-50">{{ $child['label'] }}</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    {{-- User footer --}}
    <div class="border-t border-gray-800 p-3 bg-gray-900/50">
        <div class="flex items-center gap-3 p-1">
            <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-lg shadow-indigo-500/20">
                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
            </div>
            <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                <p class="text-xs font-bold truncate">{{ auth()->user()?->name }}</p>
                <p class="text-[10px] text-gray-400 truncate uppercase font-black tracking-tighter">{{ auth()->user()?->roles?->first()?->name ?? 'User' }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- ─── MAIN AREA ────────────────────────────────────────────────────────────── --}}
<div class="flex flex-col min-h-screen transition-all duration-300"
     :class="sidebarOpen ? 'ml-64' : 'ml-16'">

    {{-- Topbar --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between h-16 px-6">
            {{-- Left: toggle + breadcrumb --}}
            {{-- Left: Logo/Toggle context removed from here, integrated into sidebar logo --}}
            <div class="flex items-center gap-4">

                {{-- Breadcrumbs --}}
                <nav class="hidden md:flex items-center gap-2 text-sm text-gray-500">
                    @isset($breadcrumbs)
                        @foreach($breadcrumbs as $crumb)
                            @if(!$loop->last)
                                <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-indigo-600 transition">{{ $crumb['label'] }}</a>
                                <i class="ph ph-caret-right text-gray-300"></i>
                            @else
                                <span class="text-gray-800 font-bold tracking-tight">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                    @endisset
                </nav>
            </div>

            {{-- Right: notifications + profile --}}
            <div class="flex items-center gap-3">
                {{-- Notification bell --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition border border-transparent hover:border-gray-200">
                        <i class="ph ph-bell text-xl"></i>
                        @php $unread = \App\Services\NotificationService::unreadCount(auth()->id() ?? 0); @endphp
                        @if($unread > 0)
                            <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden z-50">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                            <span class="font-bold text-sm text-gray-900">Notifications</span>
                            <a href="{{ route('notifications.mark-all-read') }}" class="text-[10px] font-black uppercase text-indigo-600 hover:underline">Mark all read</a>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                            @forelse(\App\Models\ErpNotification::forUser(auth()->id() ?? 0)->latest()->take(8)->get() as $notif)
                                <a href="{{ $notif->url ?: '#' }}"
                                   class="flex gap-4 px-5 py-4 hover:bg-gray-50 transition {{ $notif->isRead() ? '' : 'bg-indigo-50/30' }}">
                                    <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center shadow-sm
                                        {{ match($notif->type) {
                                            'success' => 'bg-green-100 text-green-600',
                                            'warning' => 'bg-yellow-100 text-yellow-600',
                                            'danger'  => 'bg-red-100 text-red-600',
                                            default   => 'bg-indigo-100 text-indigo-600',
                                        } }}">
                                        <i class="ph {{ $notif->icon ?: 'ph-bell' }} text-base"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $notif->title }}</p>
                                        <p class="text-xs text-gray-500 line-clamp-1 leading-relaxed">{{ $notif->body }}</p>
                                        <p class="text-[10px] font-medium text-gray-400 mt-1 uppercase tracking-tighter">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="px-5 py-10 text-center">
                                    <i class="ph ph-bell-slash text-3xl text-gray-200 mb-2"></i>
                                    <p class="text-gray-400 text-sm">No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-200 mx-1"></div>

                {{-- Profile dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-3 p-1 rounded-full hover:bg-gray-50 transition border border-transparent hover:border-gray-200">
                        <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-indigo-600/20">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                        </div>
                        <span class="hidden md:block text-sm font-bold text-gray-900 pr-1">{{ auth()->user()?->name }}</span>
                        <i class="ph ph-caret-down text-gray-400 text-[10px] mr-2"></i>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden z-50">
                        <div class="px-5 py-4 border-b border-gray-50 bg-gray-50/50">
                            <p class="text-xs font-bold text-gray-900">{{ auth()->user()?->name }}</p>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-tight mt-0.5">{{ auth()->user()?->roles?->first()?->name ?? 'User' }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                                <i class="ph ph-user-circle text-lg"></i> My Profile
                            </a>
                            <a href="{{ route('companies.index') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                                <i class="ph ph-buildings text-lg"></i> Company Details
                            </a>
                        </div>
                        <div class="border-t border-gray-50 py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-5 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 transition">
                                    <i class="ph ph-sign-out text-lg"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Header Sub-Nav --}}
        @php
            $leaveRoutes = ['employee.dashboard', 'admin.leave-types', 'admin.leave-policies', 'admin.allocations', 'admin.accrual-plans', 'manager.dashboard'];
            $controlRoutes = ['admin.dashboard', 'admin.users', 'admin.settings', 'companies.index'];

            $isLeaveSection = collect($leaveRoutes)->contains(fn($r) => request()->routeIs($r));
            $isControlSection = collect($controlRoutes)->contains(fn($r) => request()->routeIs($r));
        @endphp

        @if($isLeaveSection)
            <div class="flex items-center gap-1 px-4 pb-0.5 overflow-x-auto border-t border-gray-100 bg-gray-50/30">
                @php $leaveTabs = [
                    ['route' => 'employee.dashboard',    'label' => 'My Requests',     'icon' => 'ph-calendar'],
                    ['route' => 'manager.dashboard',     'label' => 'Approvals',       'icon' => 'ph-check-circle'],
                    ['route' => 'admin.leave-types',     'label' => 'Leave Types',     'icon' => 'ph-tag'],
                    ['route' => 'admin.leave-policies',  'label' => 'Policies',        'icon' => 'ph-file-text'],
                    ['route' => 'admin.allocations',     'label' => 'Allocations',     'icon' => 'ph-coins'],
                ]; @endphp
                @foreach($leaveTabs as $tab)
                    @php $tabActive = request()->routeIs($tab['route']); @endphp
                    @if(\Illuminate\Support\Facades\Route::has($tab['route']))
                        <a href="{{ route($tab['route']) }}"
                           class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold whitespace-nowrap border-b-2 transition
                                  {{ $tabActive ? 'border-indigo-600 text-indigo-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="ph {{ $tab['icon'] }} text-base {{ $tabActive ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                            <span class="uppercase tracking-tighter">{{ $tab['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        @if($isControlSection)
            <div class="flex items-center gap-1 px-4 pb-0.5 overflow-x-auto border-t border-gray-100 bg-gray-50/30">
                @php $controlTabs = [
                    ['route' => 'admin.dashboard',  'label' => 'Analytics',      'icon' => 'ph-gauge'],
                    ['route' => 'admin.users',       'label' => 'Users & Roles',  'icon' => 'ph-users-three'],
                    ['route' => 'companies.index',   'label' => 'Companies',      'icon' => 'ph-buildings'],
                    ['route' => 'admin.settings',    'label' => 'Configuration',  'icon' => 'ph-sliders'],
                ]; @endphp
                @foreach($controlTabs as $tab)
                    @php $tabActive = request()->routeIs($tab['route']); @endphp
                    @if(\Illuminate\Support\Facades\Route::has($tab['route']))
                        <a href="{{ route($tab['route']) }}"
                           class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold whitespace-nowrap border-b-2 transition
                                  {{ $tabActive ? 'border-indigo-600 text-indigo-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="ph {{ $tab['icon'] }} text-base {{ $tabActive ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                            <span class="uppercase tracking-tighter">{{ $tab['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </header>

    {{-- Page Content --}}
    <main class="flex-1 p-6">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 class="mb-6 flex items-center gap-3 bg-indigo-900 text-white px-5 py-4 rounded-2xl text-sm shadow-xl shadow-indigo-200">
                <i class="ph ph-check-circle text-indigo-400 text-xl flex-shrink-0"></i>
                <span class="font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto text-indigo-400 hover:text-white transition"><i class="ph ph-x-bold"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 class="mb-6 flex items-center gap-3 bg-red-600 text-white px-5 py-4 rounded-2xl text-sm shadow-xl shadow-red-200">
                <i class="ph ph-warning-circle text-red-200 text-xl flex-shrink-0"></i>
                <span class="font-medium">{{ session('error') }}</span>
                <button @click="show = false" class="ml-auto text-red-200 hover:text-white transition"><i class="ph ph-x-bold"></i></button>
            </div>
        @endif

        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 bg-white px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <span>{{ config('app.name', 'XobiyaHR') }} &copy; {{ date('Y') }}</span>
            <div class="w-1 h-1 bg-gray-200 rounded-full"></div>
            <span>Enterprise Edition</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-100">v1.2.4</span>
        </div>
    </footer>
</div>

</body>
</html>

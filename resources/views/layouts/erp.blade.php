<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} — ERP</title>

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
    </style>
</head>
<body class="h-full bg-gray-50 text-gray-800" x-data="{ sidebarOpen: true, notifOpen: false }">

{{-- ─── SIDEBAR ──────────────────────────────────────────────────────────────── --}}
<aside
    class="fixed inset-y-0 left-0 z-40 flex flex-col bg-gray-900 text-white transition-all duration-300"
    :class="sidebarOpen ? 'w-64' : 'w-16'"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-700 overflow-hidden">
        <div class="w-8 h-8 flex-shrink-0 rounded-lg bg-indigo-600 flex items-center justify-center">
            <i class="ph ph-cube text-white text-lg"></i>
        </div>
        <span x-show="sidebarOpen" x-transition class="font-bold text-lg tracking-tight whitespace-nowrap">
            {{ config('app.name', 'ERP') }}
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">
        @php
            $nav = [
                ['route' => 'dashboard',              'icon' => 'ph-squares-four',      'label' => 'Dashboard',    'perm' => null],
                ['route' => 'employees.index',         'icon' => 'ph-users',              'label' => 'Employees',    'perm' => 'employees.read'],
                ['route' => 'attendance.index',        'icon' => 'ph-clock',              'label' => 'Attendance',   'perm' => 'attendance.read'],
                ['route' => 'leave-requests.index',    'icon' => 'ph-calendar-blank',     'label' => 'Leave',        'perm' => 'leave.read'],
                ['route' => 'payroll.index',           'icon' => 'ph-money',              'label' => 'Payroll',      'perm' => 'payroll.read'],
                ['route' => 'assets.index',            'icon' => 'ph-laptop',             'label' => 'Assets',       'perm' => 'assets.read'],
                ['route' => 'inventory.index',         'icon' => 'ph-warehouse',          'label' => 'Inventory',    'perm' => 'inventory.read'],
                ['route' => 'procurement.index',       'icon' => 'ph-shopping-cart',      'label' => 'Procurement',  'perm' => 'procurement.read'],
                ['route' => 'crm.index',               'icon' => 'ph-funnel',             'label' => 'CRM',          'perm' => 'crm.read'],
                ['route' => 'sales.index',             'icon' => 'ph-chart-line-up',      'label' => 'Sales',        'perm' => 'sales.read'],
                ['route' => 'accounting.index',        'icon' => 'ph-calculator',         'label' => 'Accounting',   'perm' => 'accounting.read'],
                ['route' => 'projects.index',          'icon' => 'ph-kanban',             'label' => 'Projects',     'perm' => 'projects.read'],
                ['route' => 'helpdesk.index',          'icon' => 'ph-headset',            'label' => 'Helpdesk',     'perm' => 'helpdesk.read'],
                ['route' => 'manufacturing.index',     'icon' => 'ph-gear-six',           'label' => 'Manufacturing','perm' => 'manufacturing.read'],
                ['route' => 'reports.index',           'icon' => 'ph-chart-bar',          'label' => 'Reports',      'perm' => 'reports.read'],
            ];
        @endphp

        @foreach($nav as $item)
            @if($item['perm'] === null || auth()->user()?->hasPermission($item['perm']))
                @php $active = request()->routeIs(str_replace('.index', '.*', $item['route'])); @endphp
                @if(\Illuminate\Support\Facades\Route::has($item['route']))
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ $active ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                       title="{{ $item['label'] }}">
                        <i class="ph {{ $item['icon'] }} text-xl flex-shrink-0"></i>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">{{ $item['label'] }}</span>
                    </a>
                @else
                    <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-600 cursor-not-allowed" title="{{ $item['label'] }} (coming soon)">
                        <i class="ph {{ $item['icon'] }} text-xl flex-shrink-0"></i>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap opacity-50">{{ $item['label'] }}</span>
                    </span>
                @endif
            @endif
        @endforeach
    </nav>

    {{-- User footer --}}
    <div class="border-t border-gray-700 p-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
            </div>
            <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                <p class="text-sm font-medium truncate">{{ auth()->user()?->name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ auth()->user()?->roles?->first()?->name }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- ─── MAIN AREA ────────────────────────────────────────────────────────────── --}}
<div class="flex flex-col min-h-screen transition-all duration-300"
     :class="sidebarOpen ? 'ml-64' : 'ml-16'">

    {{-- Topbar --}}
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm">
        {{-- Left: toggle + breadcrumb --}}
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="text-gray-500 hover:text-indigo-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                <i class="ph ph-list text-xl"></i>
            </button>

            {{-- Breadcrumbs --}}
            <nav class="hidden md:flex items-center gap-2 text-sm text-gray-500">
                @isset($breadcrumbs)
                    @foreach($breadcrumbs as $crumb)
                        @if(!$loop->last)
                            <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-indigo-600 transition">{{ $crumb['label'] }}</a>
                            <i class="ph ph-caret-right text-gray-300"></i>
                        @else
                            <span class="text-gray-800 font-medium">{{ $crumb['label'] }}</span>
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
                        class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                    <i class="ph ph-bell text-xl"></i>
                    @php $unread = \App\Services\NotificationService::unreadCount(auth()->id() ?? 0); @endphp
                    @if($unread > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50">
                    <div class="flex items-center justify-between px-4 py-3 border-b">
                        <span class="font-semibold text-sm">Notifications</span>
                        <a href="{{ route('notifications.mark-all-read') }}" class="text-xs text-indigo-600 hover:underline">Mark all read</a>
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                        @forelse(\App\Models\ErpNotification::forUser(auth()->id() ?? 0)->latest()->take(8)->get() as $notif)
                            <a href="{{ $notif->url ?: '#' }}"
                               class="flex gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $notif->isRead() ? '' : 'bg-indigo-50' }}">
                                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center
                                    {{ match($notif->type) {
                                        'success' => 'bg-green-100 text-green-600',
                                        'warning' => 'bg-yellow-100 text-yellow-600',
                                        'danger'  => 'bg-red-100 text-red-600',
                                        default   => 'bg-indigo-100 text-indigo-600',
                                    } }}">
                                    <i class="ph {{ $notif->icon ?: 'ph-bell' }} text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $notif->title }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $notif->body }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-400">No notifications yet</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Profile dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                    </div>
                    <span class="hidden md:block text-sm font-medium text-gray-700">{{ auth()->user()?->name }}</span>
                    <i class="ph ph-caret-down text-gray-400 text-xs"></i>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="ph ph-user-circle"></i> My Profile
                    </a>
                    <a href="{{ route('companies.index') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="ph ph-buildings"></i> Company Settings
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50">
                            <i class="ph ph-sign-out"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Page Content --}}
    <main class="flex-1 p-6">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                <i class="ph ph-check-circle text-green-500 text-lg flex-shrink-0"></i>
                {{ session('success') }}
                <button @click="show = false" class="ml-auto text-green-600"><i class="ph ph-x"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <i class="ph ph-x-circle text-red-500 text-lg flex-shrink-0"></i>
                {{ session('error') }}
                <button @click="show = false" class="ml-auto text-red-600"><i class="ph ph-x"></i></button>
            </div>
        @endif

        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 bg-white px-6 py-3 text-xs text-gray-400 flex justify-between">
        <span>{{ config('app.name') }} ERP &copy; {{ date('Y') }}</span>
        <span>v1.0</span>
    </footer>
</div>

</body>
</html>

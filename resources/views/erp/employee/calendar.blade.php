<x-layouts.erp :title="'Personal Calendar'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Personal Calendar</h1>
            <p class="text-sm text-gray-500 mt-0.5">Monthly schedule view</p>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Today</button>
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Sync</button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="grid grid-cols-7 gap-3 text-center text-sm text-gray-500">
                <span class="font-medium text-gray-700">Mo</span><span class="font-medium text-gray-700">Tu</span><span class="font-medium text-gray-700">We</span><span class="font-medium text-gray-700">Th</span><span class="font-medium text-gray-700">Fr</span><span>Sa</span><span>Su</span>
                @for ($day = 1; $day <= 28; $day++)
                    <div class="rounded-xl border {{ in_array($day, [12, 13]) ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-100 bg-gray-50 text-gray-700' }} px-2 py-4">
                        <div class="text-sm font-semibold">{{ $day }}</div>
                        <div class="mt-2 text-[11px] text-gray-400">{{ $day === 12 ? 'PTO' : '' }}</div>
                    </div>
                @endfor
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Legend</p>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    <li class="flex items-center justify-between">
                        <span>Approved PTO</span>
                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">2 days</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span>Pending</span>
                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">1 day</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span>Team holidays</span>
                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">3 days</span>
                    </li>
                </ul>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Upcoming</p>
                <h3 class="mt-2 text-lg font-bold text-gray-900">Next events</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <p class="font-semibold text-gray-900">Annual leave</p>
                        <p class="text-xs text-gray-500">Mar 12 - Mar 13</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <p class="font-semibold text-gray-900">Team planning day</p>
                        <p class="text-xs text-gray-500">Mar 20</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</x-layouts.erp>

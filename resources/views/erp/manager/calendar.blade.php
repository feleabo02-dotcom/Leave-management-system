<x-layouts.erp :title="'Department Calendar'">
    <div class="grid gap-6 lg:grid-cols-[1.7fr_1fr]">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Department view</p>
                    <h2 class="mt-2 text-xl font-semibold">Shared calendar</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Week</button>
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Month</button>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-7 gap-2 text-center text-xs text-gray-500">
                <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
                @for ($day = 1; $day <= 28; $day++)
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-2 py-3 {{ in_array($day, [18, 19, 20, 21]) ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : '' }}">
                        <div class="text-sm font-semibold">{{ $day }}</div>
                        <div class="mt-2 text-[11px] text-gray-400">{{ in_array($day, [18, 19]) ? '3 off' : '' }}</div>
                    </div>
                @endfor
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Highlights</p>
                <h3 class="mt-2 text-lg font-semibold">Coverage insights</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <p class="font-semibold text-gray-900">Highest overlap</p>
                        <p class="text-xs text-gray-500">Feb 19 · 4 people out</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <p class="font-semibold text-gray-900">Lowest coverage</p>
                        <p class="text-xs text-gray-500">Feb 21 · 68% capacity</p>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</p>
                <h3 class="mt-2 text-lg font-semibold">Manager tools</h3>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm mt-4 w-full">Notify team</button>
                <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm mt-3 w-full">Open staffing plan</button>
            </div>
        </aside>
    </div>
</x-layouts.erp>

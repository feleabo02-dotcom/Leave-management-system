<x-layouts.erp :title="'Notifications'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500 mt-0.5">System alerts and updates</p>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Mark all read</button>
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Preferences</button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="space-y-4">
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900">Leave approved</p>
                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Your request for Feb 12-13 was approved by R. Mendez.</p>
                    <p class="mt-3 text-xs text-gray-400">2 hours ago</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900">Action required</p>
                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Please update your delegate before March leave window.</p>
                    <p class="mt-3 text-xs text-gray-400">Yesterday</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900">Policy update</p>
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Info</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Carry-forward caps updated for 2026.</p>
                    <p class="mt-3 text-xs text-gray-400">2 days ago</p>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Preferences</p>
                <h3 class="mt-2 text-lg font-bold text-gray-900">Notification channels</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span>Email alerts</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span>In-app notifications</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span>Daily digest</span>
                        <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    </label>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Help</p>
                <h3 class="mt-2 text-lg font-bold text-gray-900">Need assistance?</h3>
                <p class="mt-3 text-sm text-gray-500">Use contextual help or reach HR operations for policy questions.</p>
                <button class="mt-4 w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Open help center</button>
            </div>
        </aside>
    </div>
</x-layouts.erp>

<x-layouts.erp :title="'System Settings'">
    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">System configuration</p>
            <h2 class="mt-2 text-xl font-semibold">Global settings</h2>
            <form class="mt-6 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default approval flow</label>
                    <select class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Manager only</option>
                        <option>Manager + HR</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yearly reset date</label>
                    <input type="date" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time zone</label>
                    <select class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>UTC</option>
                        <option>GMT+1</option>
                        <option>GMT+3</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default locale</label>
                    <select class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>English (US)</option>
                        <option>English (UK)</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">HR policy notes</label>
                    <textarea class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 h-28" placeholder="Share key policy details"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Save settings</button>
                </div>
            </form>
        </div>

        <aside class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Security</p>
                <h3 class="mt-2 text-lg font-semibold">Access controls</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span>Require MFA</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-gray-200 text-indigo-600 focus:ring-indigo-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span>Session timeout</span>
                        <input type="checkbox" class="h-4 w-4 rounded border-gray-200 text-indigo-600 focus:ring-indigo-400" />
                    </label>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notifications</p>
                <h3 class="mt-2 text-lg font-semibold">Delivery channels</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span>Email alerts</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-gray-200 text-indigo-600 focus:ring-indigo-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span>In-app alerts</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-gray-200 text-indigo-600 focus:ring-indigo-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <span>Slack webhook</span>
                        <input type="checkbox" class="h-4 w-4 rounded border-gray-200 text-indigo-600 focus:ring-indigo-400" />
                    </label>
                </div>
            </div>
        </aside>
    </div>
</x-layouts.erp>

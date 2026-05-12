<x-layouts.erp :title="'Reports'">
    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Analytics</p>
                    <h2 class="mt-2 text-xl font-semibold">Leave trends</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Last 6 months</option>
                        <option>Last 12 months</option>
                    </select>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Export PDF</button>
                </div>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Top request type</p>
                    <p class="mt-2 text-lg font-semibold">Annual Leave</p>
                    <p class="text-xs text-gray-500">48% of all requests</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Average duration</p>
                    <p class="mt-2 text-lg font-semibold">2.6 days</p>
                    <p class="text-xs text-gray-500">Median across team</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approval speed</p>
                    <p class="mt-2 text-lg font-semibold">5.2 hrs</p>
                    <p class="text-xs text-gray-500">Within target SLA</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Detail report</p>
            <h3 class="mt-2 text-lg font-semibold">Department breakdown</h3>
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-4 py-3 font-medium">Team</th>
                            <th class="px-4 py-3 font-medium">Requests</th>
                            <th class="px-4 py-3 font-medium">Approved</th>
                            <th class="px-4 py-3 font-medium">Rejected</th>
                            <th class="px-4 py-3 font-medium">Avg. days</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-3 font-medium text-gray-900">Design</td>
                            <td class="px-4 py-3 text-gray-700">42</td>
                            <td class="px-4 py-3 text-gray-700">38</td>
                            <td class="px-4 py-3 text-gray-700">4</td>
                            <td class="px-4 py-3 text-gray-700">2.1</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-3 font-medium text-gray-900">Engineering</td>
                            <td class="px-4 py-3 text-gray-700">55</td>
                            <td class="px-4 py-3 text-gray-700">50</td>
                            <td class="px-4 py-3 text-gray-700">5</td>
                            <td class="px-4 py-3 text-gray-700">2.8</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-3 font-medium text-gray-900">Support</td>
                            <td class="px-4 py-3 text-gray-700">30</td>
                            <td class="px-4 py-3 text-gray-700">26</td>
                            <td class="px-4 py-3 text-gray-700">4</td>
                            <td class="px-4 py-3 text-gray-700">3.0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.erp>

<x-layouts.erp :title="'Team Availability'">
    <div class="grid gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Coverage</p>
                    <h2 class="mt-2 text-xl font-semibold">Team availability view</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>All departments</option>
                        <option>Design</option>
                        <option>Engineering</option>
                    </select>
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Export</button>
                </div>
            </div>
            <div class="overflow-x-auto mt-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-4 py-3 font-medium">Employee</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Next leave</th>
                            <th class="px-4 py-3 font-medium">Coverage status</th>
                            <th class="px-4 py-3 font-medium">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-3 font-medium text-gray-900">M. Alvarez</td>
                            <td class="px-4 py-3 text-gray-700">Product Designer</td>
                            <td class="px-4 py-3 text-gray-700">Feb 20 - Feb 21</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Limited</span></td>
                            <td class="px-4 py-3 text-gray-700">12.5 days</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-3 font-medium text-gray-900">K. Singh</td>
                            <td class="px-4 py-3 text-gray-700">Frontend Engineer</td>
                            <td class="px-4 py-3 text-gray-700">Mar 2</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Healthy</span></td>
                            <td class="px-4 py-3 text-gray-700">18 days</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-3 font-medium text-gray-900">J. Okoye</td>
                            <td class="px-4 py-3 text-gray-700">Support Lead</td>
                            <td class="px-4 py-3 text-gray-700">Mar 4 - Mar 6</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Risk</span></td>
                            <td class="px-4 py-3 text-gray-700">9 days</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Coverage alert</p>
                <h3 class="mt-2 text-lg font-semibold">Support team</h3>
                <p class="mt-2 text-sm text-gray-500">Two overlapping requests require backup staffing.</p>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm mt-4 w-full">Assign coverage</button>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Plan ahead</p>
                <h3 class="mt-2 text-lg font-semibold">March capacity</h3>
                <p class="mt-2 text-sm text-gray-500">Forecast suggests 12% drop in availability.</p>
                <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm mt-4 w-full">Review forecast</button>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tools</p>
                <h3 class="mt-2 text-lg font-semibold">Bulk actions</h3>
                <p class="mt-2 text-sm text-gray-500">Approve low-risk requests in one click.</p>
                <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm mt-4 w-full">Open bulk tools</button>
            </div>
        </div>
    </div>
</x-layouts.erp>

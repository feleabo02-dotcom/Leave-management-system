<x-layouts.erp :title="'Leave Types'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Leave Types</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure leave types and their rules.</p>
        </div>
    </div>

    <div class="grid gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Configuration</p>
                    <h2 class="mt-1 text-xl font-bold text-gray-900">Leave type library</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Paid</th>
                            <th class="px-4 py-3 font-medium">Approval</th>
                            <th class="px-4 py-3 font-medium">Carry forward</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($leaveTypes as $leaveType)
                            <tr class="hover:bg-gray-50 transition text-sm">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $leaveType->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $leaveType->is_paid ? 'Yes' : 'No' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $leaveType->requires_hr_approval ? 'Manager + HR' : 'Manager' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $leaveType->carry_forward ? 'Up to '.$leaveType->carry_forward_cap.' days' : 'No' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $leaveType->active ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $leaveType->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No leave types found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Policy editor</p>
            <h3 class="mt-1 text-xl font-bold text-gray-900">Create new type</h3>
            <form method="POST" action="{{ route('admin.leave-types.store') }}" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input name="name" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Study Leave" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Approval flow</label>
                    <select name="validation_type" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="manager">Manager only</option>
                        <option value="both">Manager + HR</option>
                        <option value="hr">HR only</option>
                        <option value="no">No validation</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paid status</label>
                    <select name="is_paid" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1">Paid</option>
                        <option value="0">Unpaid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allocation type</label>
                    <select name="allocation_type" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="fixed">Fixed allocation</option>
                        <option value="accrual">Accrual plan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Request unit</label>
                    <select name="request_unit" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="day">Day</option>
                        <option value="half_day">Half day</option>
                        <option value="hour">Hour</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allow half day</label>
                    <select name="allow_half_day" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allow hours</label>
                    <select name="allow_hour" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Accrual rate (days/month)</label>
                    <input name="accrual_rate" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="1.5" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Accrual cap (days)</label>
                    <input name="accrual_cap" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="24" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carry forward cap</label>
                    <input name="carry_forward_cap" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input name="code" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. STUDY" required />
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Save leave type</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.erp>

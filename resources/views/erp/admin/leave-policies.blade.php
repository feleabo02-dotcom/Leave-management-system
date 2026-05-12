<x-layouts.erp :title="'Leave Policies'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Leave Policies</h1>
            <p class="text-sm text-gray-500 mt-0.5">Versioned policy configuration for leave types.</p>
        </div>
    </div>

    @if (session('status') === 'leave-policy-created')
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">Leave policy created.</div>
    @endif
    @if (session('status') === 'leave-policy-activated')
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">Leave policy activated.</div>
    @endif

    <div class="grid gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Policy engine</p>
                    <h2 class="mt-1 text-xl font-bold text-gray-900">Leave policies</h2>
                </div>
                <div class="flex gap-2">
                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Versioned</span>
                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">DB configurable</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-4 py-3 font-medium">Leave Type</th>
                            <th class="px-4 py-3 font-medium">Version</th>
                            <th class="px-4 py-3 font-medium">Service Months</th>
                            <th class="px-4 py-3 font-medium">Max/Year</th>
                            <th class="px-4 py-3 font-medium">Effective</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($policies as $policy)
                            <tr class="hover:bg-gray-50 transition text-sm">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $policy->leaveType?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-700">v{{ $policy->version }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $policy->min_service_months }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $policy->max_days_per_year ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $policy->effective_from?->format('Y-m-d') }}
                                    @if ($policy->effective_to) - {{ $policy->effective_to->format('Y-m-d') }} @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $policy->is_active ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $policy->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if (!$policy->is_active)
                                        <form method="POST" action="{{ route('admin.leave-policies.activate', $policy) }}">
                                            @csrf @method('PUT')
                                            <button class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50 transition" type="submit">Activate</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No leave policies found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Create policy</p>
            <h3 class="mt-1 text-xl font-bold text-gray-900">New policy version</h3>
            <form method="POST" action="{{ route('admin.leave-policies.store') }}" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leave type</label>
                    <select name="leave_type_id" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min service months</label>
                    <input name="min_service_months" type="number" min="0" value="0" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max days per year</label>
                    <input name="max_days_per_year" type="number" min="0" step="0.5" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max unpaid days per year</label>
                    <input name="max_unpaid_days_per_year" type="number" min="0" step="0.5" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Backdate allowed</label>
                    <select name="allow_backdate" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max future apply days</label>
                    <input name="allow_future_apply_days" type="number" min="0" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yearly reset</label>
                    <select name="yearly_reset" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry days</label>
                    <input name="expiry_days" type="number" min="0" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carry-forward limit</label>
                    <input name="carry_forward_limit" type="number" min="0" step="0.5" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Activate now</label>
                    <select name="is_active" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Effective from</label>
                    <input name="effective_from" type="date" value="{{ now()->toDateString() }}" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Effective to</label>
                    <input name="effective_to" type="date" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm" type="submit">Save policy</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.erp>

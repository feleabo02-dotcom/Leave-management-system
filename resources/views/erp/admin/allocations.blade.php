<x-layouts.erp :title="'Leave Allocations'">
    <div class="grid gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Allocation engine</p>
                    <h2 class="mt-2 text-xl font-semibold">Annual allocations</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Run preview</button>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Publish allocation</button>
                </div>
            </div>
            <div class="overflow-x-auto mt-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-4 py-3 font-medium">Employee</th>
                            <th class="px-4 py-3 font-medium">Leave type</th>
                            <th class="px-4 py-3 font-medium">Allocated</th>
                            <th class="px-4 py-3 font-medium">Used</th>
                            <th class="px-4 py-3 font-medium">Remaining</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($allocations as $allocation)
                            <tr class="hover:bg-gray-50 transition text-sm">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $allocation->user->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $allocation->leaveType->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $allocation->allocated_days }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $allocation->used_days }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ max(0, ($allocation->allocated_days + $allocation->carried_over_days) - $allocation->used_days) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No allocations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Carry-forward</p>
            <h3 class="mt-2 text-lg font-semibold">Policy rules</h3>
            <form method="POST" action="{{ route('admin.allocations.store') }}" class="mt-4 grid gap-4 md:grid-cols-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select name="user_id" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leave type</label>
                    <select name="leave_type_id" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <input name="year" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ now()->format('Y') }}" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allocated days</label>
                    <input name="allocated_days" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="20" required />
                </div>
                <div class="md:col-span-4 flex justify-end">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Save allocation</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.erp>

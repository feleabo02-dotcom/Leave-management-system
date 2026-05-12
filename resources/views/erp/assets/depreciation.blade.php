<x-layouts.erp :title="'Asset Depreciation'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('assets.show', $asset) }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Asset Depreciation</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $asset->name }} &bull; {{ $asset->code }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addDepreciationModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Configure Depreciation
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="flex flex-col gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="w-16 h-16 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 mx-auto mb-4">
                    <i class="ph ph-coins text-3xl"></i>
                </div>
                <h2 class="font-bold text-gray-900 text-center text-lg">{{ $asset->name }}</h2>
                <p class="text-sm text-gray-500 text-center">{{ $asset->code }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Asset Info</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Category</p>
                        <p class="text-sm font-medium text-gray-900">{{ $asset->category->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Purchase Cost</p>
                        <p class="text-sm font-medium text-gray-900">${{ number_format($asset->purchase_cost, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Current Value</p>
                        <p class="text-sm font-medium text-gray-900">${{ number_format($asset->current_value ?? $asset->purchase_cost, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                            @if($asset->status === 'available') bg-green-100 text-green-700
                            @elseif($asset->status === 'assigned') bg-indigo-100 text-indigo-700
                            @else bg-orange-100 text-orange-700
                            @endif">
                            {{ $asset->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-table text-indigo-500"></i> Depreciation Schedule
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Year</th>
                                <th class="px-5 py-3 border-b border-gray-100">Method</th>
                                <th class="px-5 py-3 border-b border-gray-100">Annual Depreciation</th>
                                <th class="px-5 py-3 border-b border-gray-100">Accumulated</th>
                                <th class="px-5 py-3 border-b border-gray-100">Book Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($depreciationSchedule as $dep)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ $dep->year }}</td>
                                    <td class="px-5 py-3 text-sm capitalize text-gray-600">{{ $dep->method }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-900">${{ number_format($dep->annual_depreciation, 2) }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-900">${{ number_format($dep->accumulated, 2) }}</td>
                                    <td class="px-5 py-3 text-sm font-bold text-indigo-600">${{ number_format($dep->book_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No depreciation schedule configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Configure Depreciation Modal --}}
    <div id="addDepreciationModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addDepreciationModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('assets.depreciation.store', $asset) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Configure Depreciation</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Method</label>
                                <select name="method" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="straight_line">Straight Line</option>
                                    <option value="declining">Declining Balance</option>
                                    <option value="sum_of_years">Sum of Years</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Useful Life (years)</label>
                                <input type="number" name="useful_life" required min="1" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Salvage Value</label>
                                <input type="number" step="0.01" name="salvage_value" value="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Date</label>
                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addDepreciationModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

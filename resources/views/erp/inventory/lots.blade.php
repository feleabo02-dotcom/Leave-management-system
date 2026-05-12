<x-layouts.erp :title="'Lot / Serial Number Tracking'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lot / Serial Number Tracking</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track product lots and serial numbers with expiration dates.</p>
        </div>
        <button onclick="document.getElementById('createLotModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i class="ph ph-plus"></i> Create Lot
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Lots</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\StockLot::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Expiring Soon</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\StockLot::whereNotNull('expiration_date')->where('expiration_date', '>=', now())->where('expiration_date', '<=', now()->addDays(30))->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Expired</p>
            <p class="text-2xl font-bold text-red-600">{{ \App\Models\StockLot::whereNotNull('expiration_date')->where('expiration_date', '<', now())->count() }}</p>
        </div>
    </div>

    {{-- Lots Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Reference</th>
                        <th class="px-5 py-4 border-b border-gray-100">Product</th>
                        <th class="px-5 py-4 border-b border-gray-100">Expiration Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">Best Before</th>
                        <th class="px-5 py-4 border-b border-gray-100">Alert Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($lots as $lot)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $lot->name }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $lot->ref ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $lot->product->name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                @if($lot->expiration_date)
                                    <span class="text-sm {{ $lot->expiration_date < now() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                        {{ $lot->expiration_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $lot->best_before_date ? $lot->best_before_date->format('M d, Y') : '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $lot->alert_date ? $lot->alert_date->format('M d, Y') : '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $lot->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No lots found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Lot Modal --}}
    <div id="createLotModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('createLotModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('stock-lots.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Lot</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lot Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. LOT-2024-001">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reference</label>
                                <input type="text" name="ref" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Internal ref">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product</label>
                                <select name="product_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expiration Date</label>
                                <input type="date" name="expiration_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Best Before</label>
                                <input type="date" name="best_before_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alert Date</label>
                                <input type="date" name="alert_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Notes</label>
                                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Any notes about this lot..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Lot</button>
                        <button type="button" onclick="document.getElementById('createLotModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

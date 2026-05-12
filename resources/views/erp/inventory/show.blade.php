<x-layouts.erp :title="'Product - ' . $product->name">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('inventory.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Internal Reference: {{ $product->code }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAdjustModal('{{ $product->id }}', '{{ $product->name }}')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-arrows-left-right"></i> Adjust Stock
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Details & Warehouse Stock --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Product Details</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Product Type</p>
                        <p class="text-sm font-medium text-gray-900 capitalize">{{ $product->type }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Cost Price</p>
                        <p class="text-sm font-medium text-gray-900">${{ number_format($product->cost, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Sale Price</p>
                        <p class="text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Stock by Warehouse</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($product->stockLevels as $sl)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $sl->warehouse->name }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $sl->quantity }}</span>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-gray-400 text-xs italic">No stock levels recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Move History --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Inventory Movement Log</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Date</th>
                                <th class="px-5 py-3 border-b border-gray-100">Action</th>
                                <th class="px-5 py-3 border-b border-gray-100">From / To</th>
                                <th class="px-5 py-3 border-b border-gray-100">Qty</th>
                                <th class="px-5 py-3 border-b border-gray-100">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($product->stockMoves as $move)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-xs text-gray-500">{{ $move->created_at->format('M d, H:i') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase
                                            @if($move->type === 'incoming') bg-green-100 text-green-700
                                            @elseif($move->type === 'outgoing') bg-red-100 text-red-700
                                            @elseif($move->type === 'transfer') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ $move->type }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600">
                                        @if($move->fromWarehouse && $move->toWarehouse)
                                            {{ $move->fromWarehouse->code }} <i class="ph ph-arrow-right mx-1"></i> {{ $move->toWarehouse->code }}
                                        @elseif($move->fromWarehouse)
                                            {{ $move->fromWarehouse->code }}
                                        @elseif($move->toWarehouse)
                                            {{ $move->toWarehouse->code }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-sm font-bold text-gray-900">
                                        {{ $move->type === 'outgoing' ? '-' : '+' }}{{ $move->quantity }}
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-400 italic">
                                        {{ $move->reference ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No inventory movements recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Reusing Adjust Stock Modal (simplified copy or include) --}}
    <div id="adjustModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('adjustModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('inventory.adjust') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Adjust Inventory</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Warehouse</label>
                                    <select name="warehouse_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        @foreach(\App\Models\Warehouse::where('is_active', true)->get() as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Action</label>
                                    <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value="incoming">Incoming (+)</option>
                                        <option value="outgoing">Outgoing (-)</option>
                                        <option value="adjustment">Set Absolute Qty</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Quantity</label>
                                <input type="number" step="0.01" name="quantity" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reference / Note</label>
                                <input type="text" name="reference" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Apply Change</button>
                        <button type="button" onclick="document.getElementById('adjustModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAdjustModal() {
            document.getElementById('adjustModal').classList.remove('hidden');
        }
    </script>
</x-layouts.erp>

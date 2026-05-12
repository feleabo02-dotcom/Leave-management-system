<x-layouts.erp :title="'New Manufacturing Order'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('manufacturing.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Manufacturing Order</h1>
            <p class="text-sm text-gray-500 mt-0.5">Plan and execute a new production run.</p>
        </div>
    </div>

    <form action="{{ route('manufacturing.store') }}" method="POST">
        @csrf
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest">Finished Product</label>
                        <select name="product_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition appearance-none bg-gray-50" onchange="updateBOMs(this.value)">
                            <option value="">Select Finished Product...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest">Bill of Materials (BOM)</label>
                        <select name="bom_id" id="bom_select" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition appearance-none bg-gray-50">
                            <option value="">Select BOM...</option>
                            @foreach($boms as $bom)
                                <option value="{{ $bom->id }}" data-product="{{ $bom->product_id }}">{{ $bom->code }} - Produces {{ $bom->quantity }} units</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest">Production Quantity</label>
                        <input type="number" name="quantity" value="1" min="0.01" step="0.01" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition bg-gray-50">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest">Warehouse (Component Source & Destination)</label>
                        <select name="warehouse_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition appearance-none bg-gray-50">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('manufacturing.index') }}" class="px-8 py-3 border border-gray-200 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-50 transition">Cancel Plan</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl text-sm font-black hover:bg-indigo-700 transition shadow-xl shadow-indigo-200">Confirm Order</button>
            </div>
        </div>
    </form>

    <script>
        function updateBOMs(productId) {
            const select = document.getElementById('bom_select');
            const options = select.querySelectorAll('option');
            
            let firstMatch = null;
            options.forEach(opt => {
                if (!opt.value) return;
                if (opt.dataset.product == productId) {
                    opt.style.display = 'block';
                    if (!firstMatch) firstMatch = opt.value;
                } else {
                    opt.style.display = 'none';
                }
            });
            
            select.value = firstMatch || '';
        }
    </script>
</x-layouts.erp>

<x-layouts.erp :title="'New Sales Quotation'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('sales.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Sales Quotation</h1>
            <p class="text-sm text-gray-500 mt-0.5">Generate a new quotation for a customer.</p>
        </div>
    </div>

    <form action="{{ route('sales.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Header Info --}}
            <div class="lg:col-span-3">
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Customer</label>
                            <select name="customer_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="">Select Customer...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->company ?? 'No Company' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expiration Date</label>
                            <input type="date" name="date" value="{{ now()->addDays(30)->toDateString() }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Line Items --}}
            <div class="lg:col-span-3">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Order Items</h3>
                        <button type="button" onclick="addItemRow()" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                            <i class="ph ph-plus-circle"></i> Add Line Item
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="itemsTable">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                    <th class="px-5 py-3 border-b border-gray-100">Product</th>
                                    <th class="px-5 py-3 border-b border-gray-100 w-32">Quantity</th>
                                    <th class="px-5 py-3 border-b border-gray-100 w-40">Unit Price</th>
                                    <th class="px-5 py-3 border-b border-gray-100 w-40">Subtotal</th>
                                    <th class="px-5 py-3 border-b border-gray-100 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50" id="itemsContainer">
                                <tr class="item-row">
                                    <td class="px-5 py-4">
                                        <select name="items[0][product_id]" required class="w-full border-none bg-transparent text-sm focus:ring-0">
                                            <option value="">Select Product...</option>
                                            @foreach($products as $prod)
                                                <option value="{{ $prod->id }}" data-price="{{ $prod->price }}">{{ $prod->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-5 py-4">
                                        <input type="number" name="items[0][quantity]" value="1" min="0.01" step="0.01" required class="qty-input w-full border-none bg-transparent text-sm focus:ring-0">
                                    </td>
                                    <td class="px-5 py-4">
                                        <input type="number" name="items[0][unit_price]" step="0.01" required class="price-input w-full border-none bg-transparent text-sm focus:ring-0">
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="subtotal-text text-sm font-bold text-gray-900">$0.00</span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button type="button" class="text-gray-300 hover:text-red-600 transition" onclick="this.closest('tr').remove(); calculateGrandTotal();">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-5 border-t border-gray-100 bg-gray-50 flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 font-medium">Grand Total</span>
                                <span class="text-lg font-black text-indigo-600" id="grandTotal">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 flex justify-end gap-3">
                <a href="{{ route('sales.index') }}" class="px-6 py-2 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">Generate Quotation</button>
            </div>
        </div>
    </form>

    <script>
        let rowIndex = 1;

        function addItemRow() {
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('tr');
            row.className = 'item-row divide-y divide-gray-50';
            row.innerHTML = `
                <td class="px-5 py-4">
                    <select name="items[${rowIndex}][product_id]" required class="w-full border-none bg-transparent text-sm focus:ring-0">
                        <option value="">Select Product...</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" data-price="{{ $prod->price }}">{{ $prod->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-5 py-4">
                    <input type="number" name="items[${rowIndex}][quantity]" value="1" min="0.01" step="0.01" required class="qty-input w-full border-none bg-transparent text-sm focus:ring-0">
                </td>
                <td class="px-5 py-4">
                    <input type="number" name="items[${rowIndex}][unit_price]" step="0.01" required class="price-input w-full border-none bg-transparent text-sm focus:ring-0">
                </td>
                <td class="px-5 py-4">
                    <span class="subtotal-text text-sm font-bold text-gray-900">$0.00</span>
                </td>
                <td class="px-5 py-4 text-right">
                    <button type="button" class="text-gray-300 hover:text-red-600 transition" onclick="this.closest('tr').remove(); calculateGrandTotal();">
                        <i class="ph ph-trash text-lg"></i>
                    </button>
                </td>
            `;
            container.appendChild(row);
            rowIndex++;
            attachListeners();
        }

        function calculateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const sub = qty * price;
                row.querySelector('.subtotal-text').innerText = '$' + sub.toFixed(2);
                total += sub;
            });
            document.getElementById('grandTotal').innerText = '$' + total.toFixed(2);
        }

        function attachListeners() {
            document.querySelectorAll('.qty-input, .price-input').forEach(input => {
                input.oninput = calculateGrandTotal;
            });
            document.querySelectorAll('select[name*="[product_id]"]').forEach(select => {
                select.onchange = function() {
                    const price = this.options[this.selectedIndex].dataset.price || 0;
                    this.closest('tr').querySelector('.price-input').value = price;
                    calculateGrandTotal();
                }
            });
        }

        attachListeners();
    </script>
</x-layouts.erp>

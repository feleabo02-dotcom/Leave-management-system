<x-layouts.erp :title="'Lunch Orders'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lunch Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage daily lunch orders.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('createOrderModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Order
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="ph ph-check-circle text-green-500 text-lg flex-shrink-0"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-green-600"><i class="ph ph-x"></i></button>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Employee</th>
                        <th class="px-5 py-4 border-b border-gray-100">Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">Items</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $order->employee->user->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->order_date?->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->lines->count() }} item(s)</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">${{ number_format($order->total, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($order->status === 'delivered') bg-green-100 text-green-700
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    @if($order->status === 'draft')
                                        <form action="{{ route('lunch.orders.status', $order) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="text-gray-400 hover:text-blue-600 transition p-1.5" title="Confirm"><i class="ph ph-check-circle text-lg"></i></button>
                                        </form>
                                    @endif
                                    @if($order->status === 'confirmed')
                                        <form action="{{ route('lunch.orders.status', $order) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="delivered">
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition p-1.5" title="Deliver"><i class="ph ph-truck text-lg"></i></button>
                                        </form>
                                    @endif
                                    @if(in_array($order->status, ['draft', 'confirmed']))
                                        <form action="{{ route('lunch.orders.status', $order) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this order?')">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1.5" title="Cancel"><i class="ph ph-x-circle text-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No lunch orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($orders, 'links'))
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- Create Order Modal --}}
    <div id="createOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('createOrderModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('lunch.orders.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Lunch Order</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Employee</label>
                                <select name="employee_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Products</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                    @foreach($products as $product)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="products[]" value="{{ $product->id }}" class="rounded border-gray-300">
                                            <span class="text-sm text-gray-700">{{ $product->name }}</span>
                                            <span class="text-xs text-gray-400 ml-auto">${{ number_format($product->price, 2) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Create Order</button>
                        <button type="button" onclick="document.getElementById('createOrderModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

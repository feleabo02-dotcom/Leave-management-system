<x-layouts.erp :title="'Lunch Management'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lunch Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage lunch products and daily orders.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addProductModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Add Product
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Products</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\LunchProduct::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Active Products</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\LunchProduct::where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Today's Orders</p>
            <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\LunchOrder::whereDate('date', today())->count() }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ph ph-hamburger text-indigo-500"></i> Products
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Category</th>
                        <th class="px-5 py-4 border-b border-gray-100">Price</th>
                        <th class="px-5 py-4 border-b border-gray-100">Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $product->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $product->is_active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">No products added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ph ph-shopping-cart text-indigo-500"></i> Orders
            </h3>
            <a href="{{ route('lunch.orders') }}" class="text-xs font-medium text-indigo-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-3 border-b border-gray-100">Employee</th>
                        <th class="px-5 py-3 border-b border-gray-100">Date</th>
                        <th class="px-5 py-3 border-b border-gray-100">Total</th>
                        <th class="px-5 py-3 border-b border-gray-100">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $order->employee->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $order->order_date?->format('M d, Y') }}</td>
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">${{ number_format($order->total, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($order->status === 'delivered') bg-green-100 text-green-700
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Product Modal --}}
    <div id="addProductModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addProductModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('lunch.products.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Product</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category</label>
                                <select name="category_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Price</label>
                                <input type="number" step="0.01" name="price" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                            <div class="col-span-2 flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1" checked id="is_active" class="rounded border-gray-300">
                                <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addProductModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

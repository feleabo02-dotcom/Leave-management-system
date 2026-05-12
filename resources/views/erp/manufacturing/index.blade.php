<x-layouts.erp :title="'Manufacturing'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manufacturing</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage production orders and bill of materials.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manufacturing.boms') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-list"></i> View BOMs
            </a>
            <a href="{{ route('manufacturing.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Manufacturing Order
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">In Progress</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\ManufacturingOrder::where('status', 'confirmed')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Produced (Monthly)</p>
            <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\ManufacturingOrder::where('status', 'done')->whereMonth('created_at', now()->month)->sum('quantity') }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Available BOMs</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\Bom::count() }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">MO Number</th>
                        <th class="px-5 py-4 border-b border-gray-100">Product</th>
                        <th class="px-5 py-4 border-b border-gray-100">Quantity</th>
                        <th class="px-5 py-4 border-b border-gray-100">Created</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $order->code }}</td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $order->product->name }}</p>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $order->product->code }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->quantity }} Units</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($order->status === 'done') bg-green-100 text-green-700
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('manufacturing.show', $order) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                                    @if($order->status === 'confirmed')
                                        <form action="{{ route('manufacturing.complete', $order) }}" method="POST" class="inline" onsubmit="return confirm('Complete this production? This will consume components from stock.')">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition p-1.5" title="Produce / Complete"><i class="ph ph-factory text-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No manufacturing orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
</x-layouts.erp>

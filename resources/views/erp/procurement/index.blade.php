<x-layouts.erp :title="'Procurement'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Procurement</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage purchase orders and vendor relations.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('procurement.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Purchase Order
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Open Orders</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\PurchaseOrder::whereIn('status', ['draft', 'submitted', 'approved', 'ordered'])->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Spent (Year)</p>
            <p class="text-2xl font-bold text-indigo-600">${{ number_format(\App\Models\PurchaseOrder::where('status', 'received')->sum('total_amount'), 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Pending Approval</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\PurchaseOrder::where('status', 'submitted')->count() }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">PO Number</th>
                        <th class="px-5 py-4 border-b border-gray-100">Vendor</th>
                        <th class="px-5 py-4 border-b border-gray-100">Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $order->code }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->vendor->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->date->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($order->status === 'received') bg-green-100 text-green-700
                                    @elseif($order->status === 'draft') bg-gray-100 text-gray-700
                                    @elseif($order->status === 'ordered') bg-blue-100 text-blue-700
                                    @else bg-orange-100 text-orange-700
                                    @endif">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('procurement.show', $order) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                                    @if($order->status === 'ordered')
                                        <form action="{{ route('procurement.receive', $order) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition p-1.5" title="Receive Goods"><i class="ph ph-package text-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No purchase orders found.</td>
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

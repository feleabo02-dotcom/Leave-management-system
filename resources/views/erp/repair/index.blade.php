<x-layouts.erp :title="'Repair Orders'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Repair Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track and manage repair orders.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('repair.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Repair Order
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Orders</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\RepairOrder::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">In Progress</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\RepairOrder::where('status', 'in_progress')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Done</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\RepairOrder::where('status', 'done')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Cancelled</p>
            <p class="text-2xl font-bold text-red-600">{{ \App\Models\RepairOrder::where('status', 'cancelled')->count() }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Product</th>
                        <th class="px-5 py-4 border-b border-gray-100">Customer</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Priority</th>
                        <th class="px-5 py-4 border-b border-gray-100">Dates</th>
                        <th class="px-5 py-4 border-b border-gray-100">Assignee</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $order->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->product->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->customer->name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($order->status === 'done') bg-green-100 text-green-700
                                    @elseif($order->status === 'in_progress') bg-orange-100 text-orange-700
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($order->priority === 'urgent') bg-red-100 text-red-700
                                    @elseif($order->priority === 'high') bg-orange-100 text-orange-700
                                    @else bg-blue-100 text-blue-700
                                    @endif">
                                    {{ $order->priority }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $order->date_requested?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $order->assignedTo->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('repair.show', $order) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5 inline-block"><i class="ph ph-eye text-lg"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">No repair orders yet.</td>
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
</x-layouts.erp>

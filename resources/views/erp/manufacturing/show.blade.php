<x-layouts.erp :title="'Manufacturing Order - ' . $order->code">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('manufacturing.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $order->code }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Producing: {{ $order->product->name }}</p>
        </div>
        <div class="flex gap-2">
            @if($order->status === 'confirmed')
                <form action="{{ route('manufacturing.complete', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-check-circle"></i> Complete Production
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="ph ph-blueprint text-indigo-600"></i> Components to Consume
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">
                                <th class="pb-3 border-b border-gray-50">Component</th>
                                <th class="pb-3 border-b border-gray-50 text-center">Qty per Unit</th>
                                <th class="pb-3 border-b border-gray-50 text-right">Total Needed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($order->bom->lines as $line)
                                @php $needed = $line->quantity * ($order->quantity / $order->bom->quantity); @endphp
                                <tr>
                                    <td class="py-4">
                                        <p class="text-sm font-bold text-gray-900">{{ $line->product->name }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $line->product->code }}</p>
                                    </td>
                                    <td class="py-4 text-center text-sm text-gray-600">{{ $line->quantity }}</td>
                                    <td class="py-4 text-right text-sm font-black text-indigo-600">{{ number_format($needed, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar Stats --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-4 border-b border-gray-50 pb-2">Order Information</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Target Quantity</p>
                        <p class="text-xl font-black text-gray-900">{{ $order->quantity }} Units</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">BOM Used</p>
                        <p class="text-sm font-bold text-indigo-600">{{ $order->bom->code }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Location</p>
                        <p class="text-sm font-medium text-gray-900">{{ $order->warehouse->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Status</p>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase bg-indigo-100 text-indigo-700">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-4">Audit Trail</h3>
                <div class="text-[10px] space-y-2">
                    <p class="flex justify-between text-gray-500"><span>Created:</span> <span class="text-gray-900">{{ $order->created_at->format('M d, H:i') }}</span></p>
                    <p class="flex justify-between text-gray-500"><span>Planned By:</span> <span class="text-gray-900">{{ $order->creator->name }}</span></p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>

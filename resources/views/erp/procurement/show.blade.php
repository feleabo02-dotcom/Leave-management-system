<x-layouts.erp :title="'Purchase Order - ' . $order->code">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('procurement.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $order->code }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Vendor: {{ $order->vendor->name }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-printer"></i> Print PO
            </button>
            @if($order->status === 'draft')
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <i class="ph ph-paper-plane"></i> Submit for Approval
                </button>
            @elseif($order->status === 'approved')
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <i class="ph ph-shopping-cart"></i> Mark as Ordered
                </button>
            @elseif($order->status === 'ordered')
                <form action="{{ route('procurement.receive', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-package"></i> Receive Goods
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden max-w-5xl mx-auto" id="printable-po">
        <div class="p-8 border-b border-gray-100 flex justify-between items-start">
            <div>
                <h2 class="text-3xl font-black text-indigo-600 tracking-tighter mb-1">{{ config('app.name', 'XobiyaHR') }}</h2>
                <p class="text-sm text-gray-500">Procurement Department</p>
                <div class="mt-8">
                    <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">Vendor Details</p>
                    <p class="text-lg font-bold text-gray-900">{{ $order->vendor->name }}</p>
                    <p class="text-sm text-gray-600">{{ $order->vendor->email }}</p>
                    <p class="text-sm text-gray-600">{{ $order->vendor->address }}</p>
                </div>
            </div>
            <div class="text-right">
                <h3 class="text-2xl font-black text-gray-900 mb-4">PURCHASE ORDER</h3>
                <div class="space-y-1">
                    <p class="text-sm text-gray-500">PO Number: <span class="text-gray-900 font-bold">{{ $order->code }}</span></p>
                    <p class="text-sm text-gray-500">Date: <span class="text-gray-900 font-medium">{{ $order->date->format('M d, Y') }}</span></p>
                    <p class="text-sm text-gray-500">Deliver To: <span class="text-gray-900 font-medium">{{ $order->warehouse->name }}</span></p>
                    <p class="text-sm text-gray-500">Status: <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase rounded ml-1">{{ $order->status }}</span></p>
                </div>
            </div>
        </div>

        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-8 py-4 border-b border-gray-100">Product Description</th>
                        <th class="px-8 py-4 border-b border-gray-100 text-center">Quantity</th>
                        <th class="px-8 py-4 border-b border-gray-100 text-right">Unit Price</th>
                        <th class="px-8 py-4 border-b border-gray-100 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->lines as $line)
                        <tr>
                            <td class="px-8 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $line->product->name }}</p>
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-tight">{{ $line->product->code }}</p>
                            </td>
                            <td class="px-8 py-4 text-sm text-gray-900 text-center">{{ $line->quantity }}</td>
                            <td class="px-8 py-4 text-sm text-gray-900 text-right">${{ number_format($line->unit_price, 2) }}</td>
                            <td class="px-8 py-4 text-sm font-bold text-gray-900 text-right">${{ number_format($line->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-8 bg-gray-50 flex justify-between">
            <div class="max-w-xs">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Terms & Conditions</p>
                <p class="text-[10px] text-gray-500 leading-relaxed">Please send all items to the specified warehouse. Invoices should be sent to finance@xobiyahr.com referencing this PO number.</p>
            </div>
            <div class="w-64 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Before Tax</span>
                    <span class="text-gray-900 font-medium">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm border-t border-gray-200 pt-2">
                    <span class="text-gray-900 font-bold">Grand Total</span>
                    <span class="text-xl font-black text-indigo-600">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="px-8 pb-8 text-[10px] text-gray-400 text-center">
            <p>Generated by {{ $order->creator->name }} on {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #printable-po, #printable-po * { visibility: visible; }
            #printable-po {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none;
                box-shadow: none;
            }
            aside, header, footer { display: none !important; }
        }
    </style>
</x-layouts.erp>

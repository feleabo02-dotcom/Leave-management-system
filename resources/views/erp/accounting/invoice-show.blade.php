<x-layouts.erp :title="'Invoice Detail'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('accounting.invoices') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $invoice->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $invoice->partner->name ?? '—' }}</p>
        </div>
        <div class="flex gap-2">
            @if($invoice->status === 'posted')
                <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-sm flex items-center gap-2">
                    <i class="ph ph-currency-dollar"></i> Register Payment
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="ph ph-check-circle text-green-500 text-lg flex-shrink-0"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-green-600"><i class="ph ph-x"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $invoice->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $invoice->partner->name ?? '—' }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-bold rounded-full uppercase
                            @if($invoice->status === 'paid') bg-green-100 text-green-700
                            @elseif($invoice->status === 'posted') bg-orange-100 text-orange-700
                            @elseif($invoice->status === 'cancelled') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ $invoice->status }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Type</p>
                            <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $invoice->type) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date</p>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->date?->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Due Date</p>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Journal</p>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->journal->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <div class="flex justify-end">
                        <div class="w-64 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Untaxed Amount</span>
                                <span class="font-medium text-gray-900">${{ number_format($invoice->amount_untaxed, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax</span>
                                <span class="font-medium text-gray-900">${{ number_format($invoice->amount_total - $invoice->amount_untaxed, 2) }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex justify-between text-base">
                                <span class="font-bold text-gray-900">Total</span>
                                <span class="font-bold text-indigo-600">${{ number_format($invoice->amount_total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($invoice->notes)
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-sm text-gray-600">
                        {{ $invoice->notes }}
                    </div>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-currency-circle-dollar text-indigo-500"></i> Payments
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Date</th>
                                <th class="px-5 py-3 border-b border-gray-100">Amount</th>
                                <th class="px-5 py-3 border-b border-gray-100">Method</th>
                                <th class="px-5 py-3 border-b border-gray-100">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($invoice->payments as $payment)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $payment->payment_date?->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-sm font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-5 py-3 text-sm capitalize text-gray-600">{{ $payment->method ?? '—' }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $payment->ref_number ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400">No payments registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Partner Info</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Name</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->partner->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->partner->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Phone</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->partner->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Register Payment Modal --}}
    <div id="paymentModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('paymentModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('accounting.invoices.register-payment', $invoice) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Register Payment</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment Date</label>
                                <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Amount</label>
                                <input type="number" step="0.01" name="amount" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Method</label>
                                <select name="method" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="check">Check</option>
                                    <option value="credit_card">Credit Card</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reference Number</label>
                                <input type="text" name="ref_number" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:w-auto sm:text-sm">Register Payment</button>
                        <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

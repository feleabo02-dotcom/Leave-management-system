<x-layouts.erp :title="'Invoices'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage customer and vendor invoices.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addInvoiceModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Invoice
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Invoices</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\AccountInvoice::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Draft</p>
            <p class="text-2xl font-bold text-gray-600">{{ \App\Models\AccountInvoice::where('status', 'draft')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Posted</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\AccountInvoice::where('status', 'posted')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Paid</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\AccountInvoice::where('status', 'paid')->count() }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Partner</th>
                        <th class="px-5 py-4 border-b border-gray-100">Type</th>
                        <th class="px-5 py-4 border-b border-gray-100">Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">Due Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $invoice->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $invoice->partner->name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($invoice->type === 'out_invoice') bg-green-100 text-green-700
                                    @elseif($invoice->type === 'in_invoice') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    @if($invoice->type === 'out_invoice') Sale
                                    @elseif($invoice->type === 'in_invoice') Purchase
                                    @else {{ str_replace('_', ' ', $invoice->type) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $invoice->date?->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $invoice->due_date?->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">${{ number_format($invoice->amount_total, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($invoice->status === 'paid') bg-green-100 text-green-700
                                    @elseif($invoice->status === 'posted') bg-orange-100 text-orange-700
                                    @elseif($invoice->status === 'cancelled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('accounting.invoice-show', $invoice) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                                    @if($invoice->status === 'draft')
                                        <form action="{{ route('accounting.invoices.validate', $invoice) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-gray-400 hover:text-orange-600 transition p-1.5" title="Validate"><i class="ph ph-check-circle text-lg"></i></button>
                                        </form>
                                    @endif
                                    @if($invoice->status === 'posted')
                                        <form action="{{ route('accounting.invoices.register-payment', $invoice) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition p-1.5" title="Register Payment"><i class="ph ph-currency-dollar text-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($invoices, 'links'))
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    {{-- Add Invoice Modal --}}
    <div id="addInvoiceModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addInvoiceModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('accounting.invoices.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">New Invoice</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Invoice Name *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                                <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="out_invoice">Sale Invoice</option>
                                    <option value="in_invoice">Purchase Invoice</option>
                                    <option value="out_refund">Sale Refund</option>
                                    <option value="in_refund">Purchase Refund</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Partner</label>
                                <select name="partner_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                                <input type="date" name="date" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Due Date</label>
                                <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Total Amount</label>
                                <input type="number" step="0.01" name="amount_total" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Untaxed Amount</label>
                                <input type="number" step="0.01" name="amount_untaxed" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Journal</label>
                                <select name="journal_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach($journals as $journal)
                                        <option value="{{ $journal->id }}">{{ $journal->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Notes</label>
                                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addInvoiceModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

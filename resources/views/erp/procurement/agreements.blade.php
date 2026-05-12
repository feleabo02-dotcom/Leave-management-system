<x-layouts.erp :title="'Purchase Agreements'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Purchase Agreements</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage purchase agreements with vendors.</p>
        </div>
        <button onclick="document.getElementById('createAgreementModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i class="ph ph-plus"></i> Create Agreement
        </button>
    </div>

    {{-- Agreements Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Vendor</th>
                        <th class="px-5 py-4 border-b border-gray-100">Start Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">End Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total Amount</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($agreements as $agreement)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $agreement->name }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $agreement->vendor->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $agreement->start_date ? $agreement->start_date->format('M d, Y') : '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $agreement->end_date ? $agreement->end_date->format('M d, Y') : '—' }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ number_format($agreement->total_amount, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($agreement->status === 'draft') bg-gray-100 text-gray-600
                                    @elseif($agreement->status === 'active') bg-green-100 text-green-700
                                    @elseif($agreement->status === 'closed') bg-orange-100 text-orange-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $agreement->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    @if($agreement->status === 'draft')
                                        <form action="{{ route('purchase-agreements.activate', $agreement) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition p-1.5" title="Activate"><i class="ph ph-check-circle text-lg"></i></button>
                                        </form>
                                    @endif
                                    @if($agreement->status === 'active')
                                        <form action="{{ route('purchase-agreements.close', $agreement) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-orange-600 transition p-1.5" title="Close"><i class="ph ph-x-circle text-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No purchase agreements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Agreement Modal --}}
    <div id="createAgreementModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('createAgreementModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('purchase-agreements.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Purchase Agreement</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Agreement Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Annual Supply Agreement">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Vendor</label>
                                <select name="vendor_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Vendor...</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Total Amount</label>
                                <input type="number" name="total_amount" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Date</label>
                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Date</label>
                                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Terms & Conditions</label>
                                <textarea name="terms" rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Enter terms and conditions..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Agreement</button>
                        <button type="button" onclick="document.getElementById('createAgreementModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

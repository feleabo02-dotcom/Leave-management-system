<x-layouts.erp :title="'My Payslips'">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Payslips</h1>
        <p class="text-sm text-gray-500 mt-0.5">View and download your monthly salary statements.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Month / Year</th>
                        <th class="px-5 py-4 border-b border-gray-100">Net Pay</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Generated Date</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payslips as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">
                                {{ Carbon\Carbon::create(null, $item->month)->format('F') }} {{ $item->year }}
                            </td>
                            <td class="px-5 py-4 text-sm font-black text-indigo-600">${{ number_format($item->net_salary, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($item->status === 'paid') bg-green-100 text-green-700
                                    @elseif($item->status === 'approved') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-500">{{ $item->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('payroll.show', $item) }}" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 transition flex items-center gap-1">
                                        <i class="ph ph-eye"></i> View
                                    </a>
                                    <button class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition flex items-center gap-1">
                                        <i class="ph ph-download-simple"></i> Download PDF
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No payslips have been released yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.erp>

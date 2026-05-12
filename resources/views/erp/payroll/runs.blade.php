<x-layouts.erp :title="'Payroll Runs'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payroll Runs</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage payroll cycles and generate payslips.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addRunModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Payroll Run
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="ph ph-check-circle text-green-500 text-lg flex-shrink-0"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-green-600"><i class="ph ph-x"></i></button>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Period</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Employees</th>
                        <th class="px-5 py-4 border-b border-gray-100">Gross</th>
                        <th class="px-5 py-4 border-b border-gray-100">Net</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($runs as $run)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $run->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $run->period_start?->format('M d, Y') }} — {{ $run->period_end?->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($run->status === 'posted') bg-green-100 text-green-700
                                    @elseif($run->status === 'approved') bg-blue-100 text-blue-700
                                    @elseif($run->status === 'in_progress') bg-orange-100 text-orange-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $run->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $run->payslips->count() ?? 0 }}</td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-900">${{ number_format($run->gross_total ?? 0, 2) }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">${{ number_format($run->net_total ?? 0, 2) }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    @if($run->status === 'draft')
                                        <form action="{{ route('payroll.runs.generate', $run) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-indigo-600 transition p-1.5" title="Generate Payslips"><i class="ph ph-file-text text-lg"></i></button>
                                        </form>
                                        <form action="{{ route('payroll.runs.approve', $run) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="text-gray-400 hover:text-blue-600 transition p-1.5" title="Approve"><i class="ph ph-check-circle text-lg"></i></button>
                                        </form>
                                    @endif
                                    @if($run->status === 'approved')
                                        <form action="{{ route('payroll.runs.post', $run) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="posted">
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition p-1.5" title="Post"><i class="ph ph-check-square text-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No payroll runs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Payroll Run Modal --}}
    <div id="addRunModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addRunModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('payroll.runs.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">New Payroll Run</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Monthly Payroll - May 2026">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Period Start *</label>
                                <input type="date" name="period_start" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Period End *</label>
                                <input type="date" name="period_end" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Create Run</button>
                        <button type="button" onclick="document.getElementById('addRunModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

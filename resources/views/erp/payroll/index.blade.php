<x-layouts.erp :title="'Payroll Management'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payroll Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Generate and manage employee payslips.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-lightning"></i> Generate Payslips
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Payroll (Month)</p>
            <p class="text-2xl font-bold text-gray-900">
                ${{ number_format(\App\Models\Payslip::where('month', now()->month)->where('year', now()->year)->sum('net_salary'), 2) }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Payslips Generated</p>
            <p class="text-2xl font-bold text-indigo-600">
                {{ \App\Models\Payslip::where('month', now()->month)->where('year', now()->year)->count() }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Pending Approval</p>
            <p class="text-2xl font-bold text-orange-600">
                {{ \App\Models\Payslip::where('month', now()->month)->where('year', now()->year)->where('status', 'draft')->count() }}
            </p>
        </div>
    </div>

    {{-- Payslip Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex gap-2">
                <select class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="{{ now()->month }}">{{ now()->format('F') }}</option>
                </select>
                <select class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="{{ now()->year }}">{{ now()->year }}</option>
                </select>
            </div>
            <div class="relative w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search employee..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Employee</th>
                        <th class="px-5 py-4 border-b border-gray-100">Period</th>
                        <th class="px-5 py-4 border-b border-gray-100">Basic</th>
                        <th class="px-5 py-4 border-b border-gray-100">Net Salary</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payslips as $payslip)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($payslip->employee->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $payslip->employee->user->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-medium">{{ $payslip->employee->employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ Carbon\Carbon::create(null, $payslip->month)->format('M') }} {{ $payslip->year }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">${{ number_format($payslip->basic_salary, 2) }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">${{ number_format($payslip->net_salary, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($payslip->status === 'paid') bg-green-100 text-green-700
                                    @elseif($payslip->status === 'approved') bg-blue-100 text-blue-700
                                    @elseif($payslip->status === 'draft') bg-gray-100 text-gray-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $payslip->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('payroll.show', $payslip) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                                <button class="text-gray-400 hover:text-gray-600 transition p-1.5"><i class="ph ph-download-simple text-lg"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No payslips found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            {{ $payslips->links() }}
        </div>
    </div>

    {{-- Generate Modal --}}
    <div id="generateModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('generateModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('payroll.generate') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Generate Bulk Payslips</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Month</label>
                                <select name="month" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ Carbon\Carbon::create(null, $m)->format('F') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Year</label>
                                <select name="year" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="{{ now()->year }}">{{ now()->year }}</option>
                                    <option value="{{ now()->year - 1 }}">{{ now()->year - 1 }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Select Employees</label>
                            <div class="max-h-48 overflow-y-auto border border-gray-100 rounded-lg p-3 space-y-2">
                                @foreach(\App\Models\Employee::whereNotNull('salary_structure_id')->with('user')->get() as $emp)
                                    <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 p-1 rounded transition">
                                        <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                                        <span>{{ $emp->user->name }} ({{ $emp->employee_code }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Start Generation</button>
                        <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

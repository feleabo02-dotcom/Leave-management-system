<x-layouts.erp :title="'Payslip - ' . $payslip->employee->user->name">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('payroll.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Payslip Detail</h1>
            <p class="text-sm text-gray-500 mt-0.5">Reference: #PAY-{{ $payslip->id }}-{{ $payslip->year }}{{ sprintf('%02d', $payslip->month) }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-printer"></i> Print Payslip
            </button>
            @if($payslip->status === 'draft')
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <i class="ph ph-check-circle"></i> Approve & Send
                </button>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden max-w-4xl mx-auto" id="printable-payslip">
        {{-- Header --}}
        <div class="p-8 border-b border-gray-100 flex justify-between items-start">
            <div>
                <h2 class="text-3xl font-black text-indigo-600 tracking-tighter mb-1">{{ config('app.name', 'XobiyaHR') }}</h2>
                <p class="text-sm text-gray-500">Enterprise Resource Planning System</p>
                <div class="mt-6">
                    <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">Employee Details</p>
                    <p class="text-lg font-bold text-gray-900">{{ $payslip->employee->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $payslip->employee->position->title ?? 'Employee' }}</p>
                    <p class="text-sm text-gray-600">{{ $payslip->employee->department->name ?? 'No Department' }}</p>
                </div>
            </div>
            <div class="text-right">
                <h3 class="text-xl font-bold text-gray-900 mb-4">PAYSLIP</h3>
                <div class="space-y-1">
                    <p class="text-sm text-gray-500">Month: <span class="text-gray-900 font-bold">{{ Carbon\Carbon::create(null, $payslip->month)->format('F') }} {{ $payslip->year }}</span></p>
                    <p class="text-sm text-gray-500">Period: <span class="text-gray-900 font-medium">{{ $payslip->period_start->format('M d') }} - {{ $payslip->period_end->format('M d, Y') }}</span></p>
                    <p class="text-sm text-gray-500">Status: <span class="text-indigo-600 font-bold uppercase">{{ $payslip->status }}</span></p>
                </div>
            </div>
        </div>

        {{-- Breakdown --}}
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-12">
            {{-- Allowances --}}
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Allowances & Earnings</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Basic Salary</span>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($payslip->basic_salary, 2) }}</span>
                    </div>
                    @foreach($payslip->lines->where('type', 'allowance') as $line)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $line->name }}</span>
                            <span class="text-sm font-medium text-gray-900">${{ number_format($line->amount, 2) }}</span>
                        </div>
                    @endforeach
                    <div class="pt-3 border-t border-dashed border-gray-200 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-900">Gross Earnings</span>
                        <span class="text-sm font-black text-indigo-600">${{ number_format($payslip->basic_salary + $payslip->total_allowance, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Deductions --}}
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Deductions</h4>
                <div class="space-y-3">
                    @forelse($payslip->lines->where('type', 'deduction') as $line)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $line->name }}</span>
                            <span class="text-sm font-medium text-red-600">-${{ number_format($line->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">No deductions applied.</p>
                    @endforelse
                    <div class="pt-3 border-t border-dashed border-gray-200 flex justify-between items-center mt-auto">
                        <span class="text-sm font-bold text-gray-900">Total Deductions</span>
                        <span class="text-sm font-black text-red-600">-${{ number_format($payslip->total_deduction, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="mx-8 mb-8 p-6 bg-indigo-600 rounded-2xl flex justify-between items-center text-white shadow-xl shadow-indigo-100">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest opacity-80 mb-1">Net Pay Amount</p>
                <p class="text-sm opacity-90 italic">Calculated for the period of {{ $payslip->period_start->format('M Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-4xl font-black tracking-tighter">${{ number_format($payslip->net_salary, 2) }}</p>
                <p class="text-xs font-medium opacity-80 mt-1 uppercase">{{ $payslip->status === 'paid' ? 'Paid via Bank Transfer' : 'Payment Pending' }}</p>
            </div>
        </div>

        <div class="px-8 pb-8 text-[10px] text-gray-400 text-center">
            <p>This is a computer-generated document and does not require a physical signature.</p>
            <p class="mt-1">&copy; {{ date('Y') }} {{ config('app.name') }} ERP. Confidential Document.</p>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #printable-payslip, #printable-payslip * { visibility: visible; }
            #printable-payslip {
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

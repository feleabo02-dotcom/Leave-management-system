<x-layouts.erp>
    <x-slot:title>Profit & Loss Report</x-slot:title>

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Profit & Loss</h1>
                <p class="text-sm text-slate-500">Financial performance for the current period</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition flex items-center gap-2">
                    <i class="ph ph-printer"></i> Print Report
                </button>
                <button class="px-4 py-2 bg-indigo-600 rounded-xl text-xs font-bold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition flex items-center gap-2">
                    <i class="ph ph-download-simple"></i> Export PDF
                </button>
            </div>
        </div>

        {{-- Net Profit Card --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 mb-8 overflow-hidden relative">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <i class="ph ph-trend-up text-9xl text-emerald-500"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Net Profit / Loss</p>
                <h2 class="text-5xl font-black {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ number_format($netProfit, 2) }} <span class="text-2xl opacity-50">USD</span>
                </h2>
                <div class="flex gap-6 mt-6">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <p class="text-xs font-bold text-slate-500">Total Income: <span class="text-slate-900">{{ number_format($totalIncome, 2) }}</span></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                        <p class="text-xs font-bold text-slate-500">Total Expenses: <span class="text-slate-900">{{ number_format($totalExpense, 2) }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8">
            {{-- Income Section --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Income Accounts</h3>
                    <span class="text-xs font-bold text-emerald-600">+ {{ number_format($totalIncome, 2) }}</span>
                </div>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-50">
                        @foreach($incomeAccounts as $account)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-slate-900">{{ $account->name }}</p>
                                <p class="text-[10px] text-slate-400 font-mono">{{ $account->code }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xs font-bold text-slate-700">{{ number_format($account->balance, 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/50">
                        <tr>
                            <td class="px-6 py-4 text-xs font-black text-slate-900 uppercase">Total Income</td>
                            <td class="px-6 py-4 text-right text-xs font-black text-emerald-600 underline decoration-2 underline-offset-4">
                                {{ number_format($totalIncome, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Expense Section --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Expense Accounts</h3>
                    <span class="text-xs font-bold text-rose-600">- {{ number_format($totalExpense, 2) }}</span>
                </div>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-50">
                        @foreach($expenseAccounts as $account)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-slate-900">{{ $account->name }}</p>
                                <p class="text-[10px] text-slate-400 font-mono">{{ $account->code }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xs font-bold text-slate-700">{{ number_format($account->balance, 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/50">
                        <tr>
                            <td class="px-6 py-4 text-xs font-black text-slate-900 uppercase">Total Expenses</td>
                            <td class="px-6 py-4 text-right text-xs font-black text-rose-600 underline decoration-2 underline-offset-4">
                                {{ number_format($totalExpense, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Footer Info --}}
        <div class="mt-8 text-center">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">Generated by XobiyaHR Intelligence Engine</p>
        </div>
    </div>
</x-layouts.erp>

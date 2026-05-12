<x-layouts.erp :title="'Accounting Dashboard'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Accounting Overview</h1>
            <p class="text-sm text-gray-500 mt-0.5">Real-time financial status and journal entries.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('accounting.coa') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-list-numbers"></i> Chart of Accounts
            </a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Manual Journal Entry
            </button>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Assets</p>
            <p class="text-2xl font-bold text-gray-900">
                ${{ number_format(\App\Models\Account::whereIn('type', ['asset', 'bank', 'cash', 'receivable'])->get()->sum(fn($a) => $a->balance), 2) }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Liabilities</p>
            <p class="text-2xl font-bold text-red-600">
                ${{ number_format(\App\Models\Account::whereIn('type', ['liability', 'payable'])->get()->sum(fn($a) => $a->balance), 2) }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Net Income (MTD)</p>
            <p class="text-2xl font-bold text-green-600">
                ${{ number_format(
                    \App\Models\Account::where('type', 'income')->get()->sum(fn($a) => $a->balance) - 
                    \App\Models\Account::where('type', 'expense')->get()->sum(fn($a) => $a->balance), 
                    2) 
                }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Bank Balance</p>
            <p class="text-2xl font-bold text-indigo-600">
                ${{ number_format(\App\Models\Account::where('type', 'bank')->get()->sum(fn($a) => $a->balance), 2) }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Journal Entries --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Recent Journal Entries</h3>
                    <a href="{{ route('accounting.journals') }}" class="text-xs font-bold text-indigo-600 hover:underline">View All Journals</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Date</th>
                                <th class="px-5 py-3 border-b border-gray-100">Number</th>
                                <th class="px-5 py-3 border-b border-gray-100">Journal</th>
                                <th class="px-5 py-3 border-b border-gray-100">Reference</th>
                                <th class="px-5 py-3 border-b border-gray-100 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentEntries as $entry)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-xs text-gray-500">{{ $entry->date->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ $entry->code }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $entry->journal->name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-400 italic">{{ $entry->reference ?? '—' }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase {{ $entry->state === 'posted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $entry->state }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No journal entries recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Liquidity/Cash --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Bank & Cash</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($accounts->whereIn('type', ['bank', 'cash']) as $acc)
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $acc->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $acc->code }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-indigo-600">${{ number_format($acc->balance, 2) }}</p>
                                <p class="text-[10px] text-gray-400 italic">Last reconciled: Never</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>

<x-layouts.erp :title="'Accounting Report'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('reports.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 capitalize">{{ str_replace('_', ' ', $type) }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                @if($type === 'trial_balance') List of all accounts with their debit and credit balances.
                @elseif($type === 'pnl') Income and expense summary for the period.
                @elseif($type === 'balance_sheet') Assets, liabilities, and equity overview.
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <select onchange="window.location=this.value" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                <option value="{{ route('reports.accounting', ['type' => 'trial_balance']) }}" {{ $type === 'trial_balance' ? 'selected' : '' }}>Trial Balance</option>
                <option value="{{ route('reports.accounting', ['type' => 'pnl']) }}" {{ $type === 'pnl' ? 'selected' : '' }}>Profit &amp; Loss</option>
                <option value="{{ route('reports.accounting', ['type' => 'balance_sheet']) }}" {{ $type === 'balance_sheet' ? 'selected' : '' }}>Balance Sheet</option>
            </select>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if($type === 'trial_balance')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                            <th class="px-5 py-4 border-b border-gray-100">Code</th>
                            <th class="px-5 py-4 border-b border-gray-100">Account</th>
                            <th class="px-5 py-4 border-b border-gray-100">Type</th>
                            <th class="px-5 py-4 border-b border-gray-100 text-right">Debit</th>
                            <th class="px-5 py-4 border-b border-gray-100 text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($accounts as $account)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-sm text-gray-600">{{ $account->code }}</td>
                                <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ $account->name }}</td>
                                <td class="px-5 py-3 text-sm capitalize text-gray-600">{{ $account->type }}</td>
                                <td class="px-5 py-3 text-sm text-right text-red-600">{{ $account->debit > 0 ? '$' . number_format($account->debit, 2) : '—' }}</td>
                                <td class="px-5 py-3 text-sm text-right text-green-600">{{ $account->credit > 0 ? '$' . number_format($account->credit, 2) : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($type === 'pnl')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                            <th class="px-5 py-4 border-b border-gray-100">Account</th>
                            <th class="px-5 py-4 border-b border-gray-100 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="bg-green-50">
                            <td class="px-5 py-3 text-sm font-bold text-green-800" colspan="2">Income</td>
                        </tr>
                        @forelse($incomeAccounts as $acc)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-sm text-gray-700 pl-10">{{ $acc->name }}</td>
                                <td class="px-5 py-3 text-sm text-right font-medium text-green-600">${{ number_format($acc->balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-3 text-sm text-gray-400 pl-10 italic">No income accounts.</td>
                            </tr>
                        @endforelse
                        <tr class="bg-red-50">
                            <td class="px-5 py-3 text-sm font-bold text-red-800" colspan="2">Expenses</td>
                        </tr>
                        @forelse($expenseAccounts as $acc)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-sm text-gray-700 pl-10">{{ $acc->name }}</td>
                                <td class="px-5 py-3 text-sm text-right font-medium text-red-600">${{ number_format($acc->balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-3 text-sm text-gray-400 pl-10 italic">No expense accounts.</td>
                            </tr>
                        @endforelse
                        <tr class="bg-gray-50 font-bold">
                            <td class="px-5 py-4 text-sm text-gray-900">Net {{ $netIncome >= 0 ? 'Profit' : 'Loss' }}</td>
                            <td class="px-5 py-4 text-sm text-right {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format(abs($netIncome), 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @elseif($type === 'balance_sheet')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                            <th class="px-5 py-4 border-b border-gray-100">Account</th>
                            <th class="px-5 py-4 border-b border-gray-100 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="bg-blue-50">
                            <td class="px-5 py-3 text-sm font-bold text-blue-800" colspan="2">Assets</td>
                        </tr>
                        @forelse($assetAccounts as $acc)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-sm text-gray-700 pl-10">{{ $acc->name }}</td>
                                <td class="px-5 py-3 text-sm text-right font-medium text-gray-900">${{ number_format($acc->balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-3 text-sm text-gray-400 pl-10 italic">No asset accounts.</td>
                            </tr>
                        @endforelse
                        <tr class="bg-red-50">
                            <td class="px-5 py-3 text-sm font-bold text-red-800" colspan="2">Liabilities</td>
                        </tr>
                        @forelse($liabilityAccounts as $acc)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-sm text-gray-700 pl-10">{{ $acc->name }}</td>
                                <td class="px-5 py-3 text-sm text-right font-medium text-gray-900">${{ number_format($acc->balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-3 text-sm text-gray-400 pl-10 italic">No liability accounts.</td>
                            </tr>
                        @endforelse
                        <tr class="bg-green-50">
                            <td class="px-5 py-3 text-sm font-bold text-green-800" colspan="2">Equity</td>
                        </tr>
                        @forelse($equityAccounts as $acc)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-sm text-gray-700 pl-10">{{ $acc->name }}</td>
                                <td class="px-5 py-3 text-sm text-right font-medium text-gray-900">${{ number_format($acc->balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-3 text-sm text-gray-400 pl-10 italic">No equity accounts.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.erp>

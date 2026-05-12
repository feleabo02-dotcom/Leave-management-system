<x-layouts.erp :title="'Chart of Accounts'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chart of Accounts</h1>
            <p class="text-sm text-gray-500 mt-0.5">Define your organization's financial structure.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addAccountModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Create New Account
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100 w-32">Code</th>
                        <th class="px-5 py-4 border-b border-gray-100">Account Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Type</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Balance</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($accounts as $account)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $account->code }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $account->name }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if(in_array($account->type, ['income', 'equity'])) bg-green-50 text-green-700
                                    @elseif(in_array($account->type, ['expense', 'liability'])) bg-red-50 text-red-700
                                    @else bg-blue-50 text-blue-700
                                    @endif">
                                    {{ $account->type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-sm font-bold {{ $account->balance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                ${{ number_format(abs($account->balance), 2) }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-pencil-simple text-lg"></i></button>
                                <button class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-article text-lg"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Account Modal --}}
    <div id="addAccountModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addAccountModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('accounting.accounts.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Account</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Account Code</label>
                                <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. 101000">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Account Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Main Bank Account">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Account Type</label>
                                <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="bank">Bank</option>
                                    <option value="cash">Cash</option>
                                    <option value="receivable">Receivable</option>
                                    <option value="payable">Payable</option>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                    <option value="asset">Asset</option>
                                    <option value="liability">Liability</option>
                                    <option value="equity">Equity</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Account</button>
                        <button type="button" onclick="document.getElementById('addAccountModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

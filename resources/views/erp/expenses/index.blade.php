<x-layouts.erp :title="'Expense Management'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Expense Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Submit and approve expense reports.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('submitExpenseModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Submit Expense
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Expenses</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Expense::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Pending Approval</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\Expense::where('state', 'submitted')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\Expense::where('state', 'approved')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Rejected</p>
            <p class="text-2xl font-bold text-red-600">{{ \App\Models\Expense::where('state', 'rejected')->count() }}</p>
        </div>
    </div>

    {{-- Expense Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Employee</th>
                        <th class="px-5 py-4 border-b border-gray-100">Category</th>
                        <th class="px-5 py-4 border-b border-gray-100">Amount</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Date</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $expense->name }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                        {{ strtoupper(substr($expense->employee->user->name ?? 'NA', 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-gray-700 font-medium">{{ $expense->employee->user->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $expense->category->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ number_format($expense->amount, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($expense->state === 'draft') bg-gray-100 text-gray-600
                                    @elseif($expense->state === 'submitted') bg-orange-100 text-orange-700
                                    @elseif($expense->state === 'approved') bg-green-100 text-green-700
                                    @elseif($expense->state === 'rejected') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ $expense->state }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $expense->expense_date ? $expense->expense_date->format('M d, Y') : '—' }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('expenses.show', $expense) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                                    @if($expense->state === 'draft')
                                        <form action="{{ route('expenses.submit', $expense) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-indigo-600 transition p-1.5" title="Submit for Approval"><i class="ph ph-paper-plane-right text-lg"></i></button>
                                        </form>
                                    @endif
                                    @if($expense->state === 'submitted')
                                        <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition p-1.5" title="Approve"><i class="ph ph-check-circle text-lg"></i></button>
                                        </form>
                                        <form action="{{ route('expenses.reject', $expense) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1.5" title="Reject"><i class="ph ph-x-circle text-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Submit Expense Modal --}}
    <div id="submitExpenseModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('submitExpenseModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Submit Expense</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expense Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Client Lunch">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Employee</label>
                                <select name="employee_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Employee...</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category</label>
                                <select name="category_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Category...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Amount</label>
                                <input type="number" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expense Date</label>
                                <input type="date" name="expense_date" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="What was this for..."></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Notes</label>
                                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Submit Expense</button>
                        <button type="button" onclick="document.getElementById('submitExpenseModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

<x-layouts.erp :title="'Expense Detail'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('expenses.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1 mb-1">
                <i class="ph ph-arrow-left"></i> Back to Expenses
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $expense->name }}</h1>
        </div>
        <div class="flex gap-2">
            @if($expense->state === 'draft')
                <form action="{{ route('expenses.submit', $expense) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-paper-plane-right"></i> Submit for Approval
                    </button>
                </form>
            @endif
            @if($expense->state === 'submitted')
                <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-check-circle"></i> Approve
                    </button>
                </form>
                <form action="{{ route('expenses.reject', $expense) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-x-circle"></i> Reject
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-4">Expense Information</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Name</p>
                            <p class="text-sm font-bold text-gray-900">{{ $expense->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Category</p>
                            <p class="text-sm text-gray-700">{{ $expense->category->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Amount</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($expense->amount, 2) }} {{ $expense->currency ?? 'USD' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Status</p>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                @if($expense->state === 'draft') bg-gray-100 text-gray-600
                                @elseif($expense->state === 'submitted') bg-orange-100 text-orange-700
                                @elseif($expense->state === 'approved') bg-green-100 text-green-700
                                @elseif($expense->state === 'rejected') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $expense->state }}
                            </span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-4">Employee & Dates</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Employee</p>
                            <p class="text-sm font-bold text-gray-900">{{ $expense->employee->user->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Expense Date</p>
                            <p class="text-sm text-gray-700">{{ $expense->expense_date ? $expense->expense_date->format('F d, Y') : '—' }}</p>
                        </div>
                        @if($expense->approval_date)
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Approval Date</p>
                                <p class="text-sm text-gray-700">{{ $expense->approval_date->format('F d, Y') }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Created</p>
                            <p class="text-sm text-gray-700">{{ $expense->created_at->format('F d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($expense->description)
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h3 class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-2">Description</h3>
                    <p class="text-sm text-gray-700">{{ $expense->description }}</p>
                </div>
            @endif

            @if($expense->notes)
                <div class="mt-4">
                    <h3 class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-2">Notes</h3>
                    <p class="text-sm text-gray-700">{{ $expense->notes }}</p>
                </div>
            @endif

            {{-- Status Timeline --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-4">Status Timeline</h3>
                <div class="flex items-center gap-2 text-xs">
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">Draft</span>
                    <i class="ph ph-caret-right text-gray-300"></i>
                    <span class="px-2 py-0.5 rounded-full font-medium
                        @if(in_array($expense->state, ['submitted', 'approved', 'rejected'])) bg-orange-100 text-orange-700 @else bg-gray-100 text-gray-400 @endif">
                        Submitted
                    </span>
                    <i class="ph ph-caret-right text-gray-300"></i>
                    <span class="px-2 py-0.5 rounded-full font-medium
                        @if($expense->state === 'approved') bg-green-100 text-green-700
                        @elseif($expense->state === 'rejected') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-400 @endif">
                        {{ $expense->state === 'rejected' ? 'Rejected' : 'Approved' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>

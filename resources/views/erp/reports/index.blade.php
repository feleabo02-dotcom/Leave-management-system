<x-layouts.erp :title="'Reports'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
            <p class="text-sm text-gray-500 mt-0.5">Generate and view business reports.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('reports.payroll-summary') }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition group">
            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-700 flex items-center justify-center mb-4">
                <i class="ph ph-currency-dollar text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">Payroll Summary</h3>
            <p class="text-sm text-gray-500 mt-1">Monthly payroll totals including gross, deductions, and net pay.</p>
        </a>

        <a href="{{ route('reports.attendance-summary') }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition group">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-4">
                <i class="ph ph-clock text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">Attendance Summary</h3>
            <p class="text-sm text-gray-500 mt-1">Monthly attendance analytics including late and overtime.</p>
        </a>

        <a href="{{ route('reports.accounting', ['type' => 'trial_balance']) }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition group">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center mb-4">
                <i class="ph ph-list-numbers text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">Trial Balance</h3>
            <p class="text-sm text-gray-500 mt-1">List of all accounts with debit and credit balances.</p>
        </a>

        <a href="{{ route('reports.accounting', ['type' => 'pnl']) }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition group">
            <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center mb-4">
                <i class="ph ph-chart-bar text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">Profit &amp; Loss</h3>
            <p class="text-sm text-gray-500 mt-1">Income and expense summary for selected period.</p>
        </a>

        <a href="{{ route('reports.accounting', ['type' => 'balance_sheet']) }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition group">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mb-4">
                <i class="ph ph-calculator text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">Balance Sheet</h3>
            <p class="text-sm text-gray-500 mt-1">Assets, liabilities, and equity overview.</p>
        </a>
    </div>
</x-layouts.erp>

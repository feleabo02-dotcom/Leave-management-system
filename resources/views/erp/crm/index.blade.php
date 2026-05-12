<x-layouts.erp :title="'CRM - Customers'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers & Leads</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your relationship with leads and existing customers.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('crm.pipeline') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-kanban"></i> View Pipeline
            </a>
            <button onclick="document.getElementById('addCustomerModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Add Lead/Customer
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex gap-2">
                <select class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option>All Types</option>
                    <option>Lead</option>
                    <option>Customer</option>
                </select>
            </div>
            <div class="relative w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search customers..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Customer / Lead</th>
                        <th class="px-5 py-4 border-b border-gray-100">Contact Info</th>
                        <th class="px-5 py-4 border-b border-gray-100">Company</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-center">Opps</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $customer->type === 'lead' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' }} flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $customer->name }}</p>
                                        <span class="text-[10px] font-bold uppercase tracking-tight {{ $customer->type === 'lead' ? 'text-orange-500' : 'text-green-500' }}">{{ $customer->type }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                <p>{{ $customer->email }}</p>
                                <p class="text-[10px] text-gray-400">{{ $customer->phone ?? 'No phone' }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $customer->company ?? '—' }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">{{ $customer->opportunities_count }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $customer->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $customer->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-note-pencil text-lg"></i></button>
                                <button onclick="openOppModal('{{ $customer->id }}', '{{ $customer->name }}')" class="text-gray-400 hover:text-indigo-600 transition p-1.5" title="Add Opportunity"><i class="ph ph-currency-circle-dollar text-lg"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No customers or leads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            {{ $customers->links() }}
        </div>
    </div>

    {{-- Add Customer Modal --}}
    <div id="addCustomerModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addCustomerModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('crm.customers.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Lead/Customer</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Address</label>
                                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Phone Number</label>
                                <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Company</label>
                                <input type="text" name="company" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                                <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="lead">Lead</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Save Record</button>
                        <button type="button" onclick="document.getElementById('addCustomerModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Opportunity Modal --}}
    <div id="oppModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('oppModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('crm.opportunities.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" id="opp_customer_id">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">New Opportunity</h3>
                        <p class="text-sm text-gray-500 mb-4" id="opp_customer_name"></p>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Opportunity Title</label>
                                <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Enterprise License Deal">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expected Revenue ($)</label>
                                    <input type="number" step="0.01" name="expected_revenue" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expected Closing</label>
                                    <input type="date" name="closing_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Opportunity</button>
                        <button type="button" onclick="document.getElementById('oppModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openOppModal(id, name) {
            document.getElementById('opp_customer_id').value = id;
            document.getElementById('opp_customer_name').innerText = 'For ' + name;
            document.getElementById('oppModal').classList.remove('hidden');
        }
    </script>
</x-layouts.erp>

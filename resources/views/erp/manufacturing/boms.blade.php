<x-layouts.erp :title="'Bill of Materials'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bill of Materials</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage product BOMs and component lists.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addBomModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New BOM
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Product</th>
                        <th class="px-5 py-4 border-b border-gray-100">Code</th>
                        <th class="px-5 py-4 border-b border-gray-100">Quantity</th>
                        <th class="px-5 py-4 border-b border-gray-100">Components</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($boms as $bom)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $bom->product->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $bom->code }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $bom->quantity }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $bom->components->count() }} component(s)</td>
                            <td class="px-5 py-4 text-right">
                                <button onclick="document.getElementById('bomLines{{ $bom->id }}').classList.toggle('hidden')" class="text-gray-400 hover:text-indigo-600 transition p-1.5">
                                    <i class="ph ph-list-bullets text-lg"></i>
                                </button>
                            </td>
                        </tr>
                        <tr id="bomLines{{ $bom->id }}" class="hidden bg-gray-50">
                            <td colspan="5" class="px-5 py-4">
                                <div class="text-xs font-bold uppercase text-gray-500 mb-2">Components</div>
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">
                                            <th class="pb-2 pr-4">Component</th>
                                            <th class="pb-2 pr-4">Qty</th>
                                            <th class="pb-2 pr-4">Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($bom->components as $component)
                                            <tr>
                                                <td class="py-1.5 pr-4 text-sm text-gray-700">{{ $component->product->name ?? '—' }}</td>
                                                <td class="py-1.5 pr-4 text-sm text-gray-700">{{ $component->quantity }}</td>
                                                <td class="py-1.5 pr-4 text-sm text-gray-700">{{ $component->unit ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="py-2 text-sm text-gray-400 italic">No components.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No BOMs created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add BOM Modal --}}
    <div id="addBomModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addBomModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('manufacturing.boms.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">New Bill of Materials</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product *</label>
                                <select name="product_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code *</label>
                                <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Quantity</label>
                                <input type="number" step="0.01" name="quantity" value="1" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addBomModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

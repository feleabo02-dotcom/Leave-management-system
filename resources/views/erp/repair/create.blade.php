<x-layouts.erp :title="'New Repair Order'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('repair.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Repair Order</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a new repair order for a product.</p>
        </div>
    </div>

    <form action="{{ route('repair.store') }}" method="POST" class="max-w-4xl bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @csrf
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ph ph-wrench text-indigo-500"></i> Repair Order Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Name *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. REP-2024-001">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="product_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    @error('product_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                    <select name="customer_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="">Select Technician</option>
                        @foreach($users as $tech)
                            <option value="{{ $tech->id }}" {{ old('assigned_to') == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Requested</label>
                    <input type="date" name="date_requested" value="{{ old('date_requested', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('date_requested') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis</label>
                    <textarea name="diagnosis" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Describe the issue...">{{ old('diagnosis') }}</textarea>
                    @error('diagnosis') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-white flex justify-end gap-3">
            <a href="{{ route('repair.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Create Repair Order</button>
        </div>
    </form>
</x-layouts.erp>

<x-layouts.erp :title="'Edit Equipment'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('maintenance.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Equipment</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update equipment details for {{ $equipment->name }}.</p>
        </div>
    </div>

    <form action="{{ route('maintenance.equipment.update', $equipment) }}" method="POST" class="max-w-4xl bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ph ph-toolbox text-indigo-500"></i> Equipment Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $equipment->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                    <input type="text" name="code" value="{{ old('code', $equipment->code) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $equipment->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="operating" {{ old('status', $equipment->status) === 'operating' ? 'selected' : '' }}>Operating</option>
                        <option value="under_maintenance" {{ old('status', $equipment->status) === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        <option value="out_of_service" {{ old('status', $equipment->status) === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="">Select</option>
                        <option value="warehouse_a" {{ old('location', $equipment->location) === 'warehouse_a' ? 'selected' : '' }}>Warehouse A</option>
                        <option value="warehouse_b" {{ old('location', $equipment->location) === 'warehouse_b' ? 'selected' : '' }}>Warehouse B</option>
                        <option value="production_floor" {{ old('location', $equipment->location) === 'production_floor' ? 'selected' : '' }}>Production Floor</option>
                        <option value="office" {{ old('location', $equipment->location) === 'office' ? 'selected' : '' }}>Office</option>
                        <option value="outdoor" {{ old('location', $equipment->location) === 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                    </select>
                    @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('purchase_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Cost</label>
                    <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost', $equipment->purchase_cost) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('purchase_cost') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ph ph-notepad text-indigo-500"></i> Notes
            </h2>
            <div>
                <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes', $equipment->notes) }}</textarea>
                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="px-6 py-4 bg-white flex justify-end gap-3">
            <a href="{{ route('maintenance.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Update Equipment</button>
        </div>
    </form>
</x-layouts.erp>

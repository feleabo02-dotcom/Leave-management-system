<x-layouts.erp :title="'Edit Vehicle'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('fleet.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Vehicle</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update vehicle information for {{ $vehicle->name }}.</p>
        </div>
    </div>

    <form action="{{ route('fleet.update', $vehicle) }}" method="POST" class="max-w-4xl bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ph ph-truck text-indigo-500"></i> Vehicle Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Name *</label>
                    <input type="text" name="name" value="{{ old('name', $vehicle->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License Plate *</label>
                    <input type="text" name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('license_plate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model *</label>
                    <select name="model_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="">Select Model</option>
                        @foreach($brands as $brand)
                            <optgroup label="{{ $brand->name }}">
                                @foreach($brand->models as $model)
                                    <option value="{{ $model->id }}" {{ old('model_id', $vehicle->model_id) == $model->id ? 'selected' : '' }}>{{ $model->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('model_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Driver</label>
                    <select name="driver_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="">No Driver Assigned</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ old('driver_id', $vehicle->driver_id) == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endforeach
                    </select>
                    @error('driver_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Acquisition Date</label>
                    <input type="date" name="acquisition_date" value="{{ old('acquisition_date', $vehicle->acquisition_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('acquisition_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Acquisition Cost</label>
                    <input type="number" step="0.01" name="acquisition_cost" value="{{ old('acquisition_cost', $vehicle->acquisition_cost) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('acquisition_cost') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('color') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">VIN Number</label>
                    <input type="text" name="vin_number" value="{{ old('vin_number', $vehicle->vin_number) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('vin_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seats</label>
                    <input type="number" name="seats" value="{{ old('seats', $vehicle->seats) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('seats') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        <option value="active" {{ old('status', $vehicle->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="maintenance" {{ old('status', $vehicle->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="retired" {{ old('status', $vehicle->status) === 'retired' ? 'selected' : '' }}>Retired</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ph ph-notepad text-indigo-500"></i> Notes
            </h2>
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Any additional information...">{{ old('notes', $vehicle->notes) }}</textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-white flex justify-end gap-3">
            <a href="{{ route('fleet.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Update Vehicle</button>
        </div>
    </form>
</x-layouts.erp>

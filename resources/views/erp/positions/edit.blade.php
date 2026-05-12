<x-layouts.erp :title="'Edit Position'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('positions.show', $position) }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Position</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update {{ $position->title }}.</p>
        </div>
    </div>

    <form action="{{ route('positions.update', $position) }}" method="POST" class="max-w-2xl bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" required value="{{ old('title', $position->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                    <option value="">No Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $position->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                <input type="number" name="level" required min="1" value="{{ old('level', $position->level) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description', $position->description) }}</textarea>
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
            <a href="{{ route('positions.show', $position) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Save Changes</button>
        </div>
    </form>
</x-layouts.erp>

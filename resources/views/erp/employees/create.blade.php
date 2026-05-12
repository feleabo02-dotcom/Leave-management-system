<x-layouts.erp :title="'Add Employee'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('employees.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Employee</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a new employee profile and generate their credentials.</p>
        </div>
    </div>

    <form action="{{ route('employees.store') }}" method="POST" class="max-w-4xl">
        @csrf

        <div x-data="{ open: true }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-4">
            <button type="button" @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 border-b border-gray-100 hover:bg-gray-100 transition">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ph ph-user-circle text-indigo-500"></i> Personal Information
                </h2>
                <i class="ph ph-caret-down text-gray-400 transition" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" required value="{{ old('first_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" required value="{{ old('last_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" name="email" required value="{{ old('email') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee Code *</label>
                        <input type="text" name="employee_code" required value="{{ old('employee_code', 'EMP-' . strtoupper(Str::random(5))) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('employee_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('dob') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ open: true }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-4">
            <button type="button" @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 border-b border-gray-100 hover:bg-gray-100 transition">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ph ph-briefcase text-indigo-500"></i> Work Information
                </h2>
                <i class="ph ph-caret-down text-gray-400 transition" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                        <select name="department_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position / Title</label>
                        <select name="position_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                            <option value="">Select Position</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                            @endforeach
                        </select>
                        @error('position_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Direct Manager</label>
                        <select name="manager_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                            <option value="">No Manager</option>
                            @foreach($managers as $mgr)
                                <option value="{{ $mgr->id }}" {{ old('manager_id') == $mgr->id ? 'selected' : '' }}>{{ $mgr->name }}</option>
                            @endforeach
                        </select>
                        @error('manager_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hire Date *</label>
                        <input type="date" name="hire_date" required value="{{ old('hire_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('hire_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Work Schedule / Shift</label>
                        <select name="shift_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                            <option value="">Select Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                            @endforeach
                        </select>
                        @error('shift_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ open: false }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-4">
            <button type="button" @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 border-b border-gray-100 hover:bg-gray-100 transition">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ph ph-gear text-indigo-500"></i> HR Settings
                </h2>
                <i class="ph ph-caret-down text-gray-400 transition" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salary Structure</label>
                        <select name="salary_structure_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                            <option value="">Select</option>
                            @foreach($salaryStructures as $ss)
                                <option value="{{ $ss->id }}" {{ old('salary_structure_id') == $ss->id ? 'selected' : '' }}>{{ $ss->name }}</option>
                            @endforeach
                        </select>
                        @error('salary_structure_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact (JSON)</label>
                        <textarea name="emergency_contact" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder='{"name":"John Doe","phone":"+123456789","relation":"Spouse"}'>{{ old('emergency_contact') }}</textarea>
                        @error('emergency_contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Save Employee</button>
        </div>
    </form>
</x-layouts.erp>

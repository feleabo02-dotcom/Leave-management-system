<x-layouts.erp :title="'Employee Profile'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('employees.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $employee->user?->name ?? 'Unknown' }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $employee->position?->title ?? 'Employee' }} &bull; {{ $employee->department?->name ?? 'No Department' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-pencil-simple"></i> Edit Profile
            </a>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="ph ph-check-circle text-green-500 text-lg flex-shrink-0"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-green-600"><i class="ph ph-x"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Sidebar: Basic Info --}}
        <div class="flex flex-col gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col items-center text-center">
                <div class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-3xl mb-4">
                    {{ strtoupper(substr($employee->user?->name ?? 'U', 0, 2)) }}
                </div>
                <h2 class="font-bold text-gray-900 text-lg">{{ $employee->user?->name }}</h2>
                <p class="text-sm text-gray-500">{{ $employee->employee_code }}</p>
                <div class="mt-4">
                    @if($employee->status === 'active')
                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Active</span>
                    @elseif($employee->status === 'probation')
                        <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Probation</span>
                    @elseif($employee->status === 'suspended')
                        <span class="px-3 py-1 text-xs font-medium bg-orange-100 text-orange-700 rounded-full">Suspended</span>
                    @elseif($employee->status === 'terminated')
                        <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Terminated</span>
                    @else
                        <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ ucfirst($employee->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-address-book text-indigo-500"></i> Contact Info
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $employee->user?->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Phone</p>
                        <p class="text-sm font-medium text-gray-900">{{ $employee->user?->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Hire Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $employee->hire_date?->format('M d, Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Manager</p>
                        <p class="text-sm font-medium text-gray-900">
                            @if($employee->manager)
                                {{ $employee->manager->name }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Position</p>
                        <p class="text-sm font-medium text-gray-900">{{ $employee->position?->title ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Content: Contracts, Documents, History --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Contracts --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-file-text text-indigo-500"></i> Contracts
                    </h3>
                    <button onclick="document.getElementById('addContractForm').classList.toggle('hidden')" class="text-xs font-medium text-indigo-600 hover:underline">+ Add Contract</button>
                </div>

                <div id="addContractForm" class="hidden p-5 border-b border-gray-100 bg-indigo-50">
                    <form action="{{ route('employees.contracts.store', $employee) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type *</label>
                            <select name="type" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                                <option value="">Select</option>
                                <option value="permanent">Permanent</option>
                                <option value="contract">Contract</option>
                                <option value="probation">Probation</option>
                                <option value="internship">Internship</option>
                                <option value="consultant">Consultant</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Salary</label>
                            <input type="number" step="0.01" name="salary" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Date *</label>
                            <input type="date" name="start_date" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Contract File (PDF)</label>
                            <input type="file" name="contract_file" accept=".pdf,.doc,.docx" class="w-full text-sm">
                        </div>
                        <div class="md:col-span-2 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('addContractForm').classList.add('hidden')" class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg">Cancel</button>
                            <button type="submit" class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg">Save Contract</button>
                        </div>
                    </form>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($employee->contracts as $contract)
                        <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                                    <i class="ph ph-file-text"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 capitalize">{{ $contract->type }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $contract->start_date->format('M d, Y') }}
                                        @if($contract->end_date) — {{ $contract->end_date->format('M d, Y') }} @endif
                                        @if($contract->salary) &bull; {{ number_format($contract->salary, 2) }} @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($contract->is_active)
                                    <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                                @endif
                                @if($contract->file_path)
                                    <a href="{{ asset('storage/' . $contract->file_path) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-indigo-600">
                                        <i class="ph ph-download-simple text-lg"></i>
                                    </a>
                                @endif
                                <form action="{{ route('employees.contracts.destroy', [$employee, $contract]) }}" method="POST" onsubmit="return confirm('Delete this contract?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-gray-500 hover:text-red-600"><i class="ph ph-trash text-lg"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No contracts yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Documents --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-folder text-indigo-500"></i> Documents
                    </h3>
                    <button onclick="document.getElementById('addDocForm').classList.toggle('hidden')" class="text-xs font-medium text-indigo-600 hover:underline">+ Upload Document</button>
                </div>

                <div id="addDocForm" class="hidden p-5 border-b border-gray-100 bg-indigo-50">
                    <form action="{{ route('employees.documents.store', $employee) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type *</label>
                            <select name="type" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                                <option value="">Select</option>
                                <option value="id">ID / Passport</option>
                                <option value="certificate">Certificate</option>
                                <option value="resume">Resume / CV</option>
                                <option value="contract_attachment">Contract Attachment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label>
                            <input type="date" name="expiry_date" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">File *</label>
                            <input type="file" name="document_file" required class="w-full text-sm">
                        </div>
                        <div class="md:col-span-2 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('addDocForm').classList.add('hidden')" class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg">Cancel</button>
                            <button type="submit" class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg">Upload</button>
                        </div>
                    </form>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($employee->documents as $doc)
                        <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                                    <i class="ph ph-file"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $doc->type) }}</p>
                                    @if($doc->expiry_date)
                                        <p class="text-xs {{ $doc->expiry_date->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                            Expires {{ $doc->expiry_date->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($doc->file_path)
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-indigo-600">
                                        <i class="ph ph-download-simple text-lg"></i>
                                    </a>
                                @endif
                                <form action="{{ route('employees.documents.destroy', [$employee, $doc]) }}" method="POST" onsubmit="return confirm('Delete this document?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-gray-500 hover:text-red-600"><i class="ph ph-trash text-lg"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No documents uploaded yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Job History --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-clock-counter-clockwise text-indigo-500"></i> Job History
                    </h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($employee->histories as $history)
                        <div class="flex items-start gap-3 px-5 py-3.5">
                            <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mt-0.5 flex-shrink-0">
                                <i class="ph ph-arrow-right"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $history->change_type) }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    @if($history->old_value || $history->new_value)
                                        @foreach($history->new_value ?? [] as $field => $newVal)
                                            @php $oldVal = $history->old_value[$field] ?? null; @endphp
                                            @if($oldVal !== $newVal)
                                                <span class="block">{{ ucfirst($field) }}: <span class="text-red-500 line-through">{{ $oldVal ?? '—' }}</span> → <span class="text-green-600">{{ $newVal }}</span></span>
                                            @endif
                                        @endforeach
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $history->created_at->diffForHumans() }}
                                    @if($history->changer) by {{ $history->changer->name }} @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No history recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>

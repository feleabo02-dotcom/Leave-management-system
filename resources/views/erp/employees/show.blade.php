<x-layouts.erp :title="'Employee Profile'">
    <div x-data="{ tab: 'personal' }">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('employees.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
                <i class="ph ph-arrow-left"></i>
            </a>
            <div class="flex-1 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($employee->user?->name ?? 'U', 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $employee->user?->name ?? 'Unknown' }}</h1>
                    <p class="text-sm text-gray-500">{{ $employee->position?->title ?? 'Employee' }} &bull; {{ $employee->department?->name ?? 'No Department' }}</p>
                </div>
            </div>
            <div class="flex gap-2 items-center">
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
                <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                    <i class="ph ph-pencil-simple"></i> Edit
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

        {{-- Tab Navigation (Odoo-style) --}}
        <div class="bg-white border border-gray-200 rounded-t-xl shadow-sm overflow-hidden">
            <div class="flex overflow-x-auto border-b border-gray-200 bg-gray-50">
                <button @click="tab = 'personal'" :class="tab === 'personal' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-user-circle"></i> Personal Info
                </button>
                <button @click="tab = 'work'" :class="tab === 'work' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-briefcase"></i> Work
                </button>
                <button @click="tab = 'private'" :class="tab === 'private' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-lock"></i> Private Info
                </button>
                <button @click="tab = 'hr-settings'" :class="tab === 'hr-settings' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-gear"></i> HR Settings
                </button>
                <button @click="tab = 'contracts'" :class="tab === 'contracts' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-file-text"></i> Contracts
                </button>
                <button @click="tab = 'skills'" :class="tab === 'skills' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-lightbulb"></i> Skills & Resume
                </button>
                <button @click="tab = 'documents'" :class="tab === 'documents' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-folder"></i> Documents
                </button>
                <button @click="tab = 'attendance'" :class="tab === 'attendance' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-clock"></i> Attendance
                </button>
                <button @click="tab = 'leave'" :class="tab === 'leave' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-calendar-blank"></i> Leave
                </button>
                <button @click="tab = 'payroll'" :class="tab === 'payroll' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-currency-dollar"></i> Payroll
                </button>
                <button @click="tab = 'assets'" :class="tab === 'assets' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-devices"></i> Assets
                </button>
                <button @click="tab = 'history'" :class="tab === 'history' ? 'border-indigo-600 text-indigo-700 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-white/50'" class="flex items-center gap-2 px-5 py-3 text-sm border-b-2 transition whitespace-nowrap">
                    <i class="ph ph-clock-counter-clockwise"></i> History
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-6">

                <!-- TAB: Personal Information -->
                <div x-show="tab === 'personal'" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1 flex flex-col items-center">
                            <div class="w-32 h-32 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-4xl mb-3">
                                {{ strtoupper(substr($employee->user?->name ?? 'U', 0, 2)) }}
                            </div>
                            <h2 class="font-bold text-gray-900 text-lg">{{ $employee->user?->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $employee->employee_code }}</p>
                        </div>
                        <div class="md:col-span-2 space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Full Name</p>
                                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->user?->name ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Email</p>
                                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->user?->email ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Phone</p>
                                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->user?->phone ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Date of Birth</p>
                                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->dob?->format('M d, Y') ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Gender</p>
                                    <p class="text-sm font-medium text-gray-900 mt-0.5 capitalize">{{ $employee->gender ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Employee Code</p>
                                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->employee_code }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: Work Information -->
                <div x-show="tab === 'work'" x-transition>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Department</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->department?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Position / Job Title</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->position?->title ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Direct Manager</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->manager?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Hire Date</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->hire_date?->format('M d, Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Work Schedule / Shift</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->shift?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Company</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->company?->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <!-- TAB: Private Information -->
                <div x-show="tab === 'private'" x-transition>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Emergency Contact</p>
                            @if($employee->emergency_contact)
                                <div class="mt-1 space-y-1 text-sm text-gray-900">
                                    <p><span class="font-medium">Name:</span> {{ $employee->emergency_contact['name'] ?? '—' }}</p>
                                    <p><span class="font-medium">Phone:</span> {{ $employee->emergency_contact['phone'] ?? '—' }}</p>
                                    <p><span class="font-medium">Relation:</span> {{ $employee->emergency_contact['relation'] ?? '—' }}</p>
                                </div>
                            @else
                                <p class="text-sm text-gray-400 mt-1">—</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Bank Account</p>
                            <p class="text-sm text-gray-400 mt-1">Not configured</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Tax ID (TIN/SSS)</p>
                            <p class="text-sm text-gray-400 mt-1">Not configured</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Passport / ID Number</p>
                            <p class="text-sm text-gray-400 mt-1">Not configured</p>
                        </div>
                    </div>
                </div>

                <!-- TAB: HR Settings -->
                <div x-show="tab === 'hr-settings'" x-transition>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Status</p>
                            <div class="mt-1">
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
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Salary Structure</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $employee->salaryStructure?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Base Salary</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $employee->salaryStructure?->base_salary ? '$' . number_format($employee->salaryStructure->base_salary, 2) : '—' }}</p>
                        </div>
                    </div>
                </div>

                <!-- TAB: Contracts -->
                <div x-show="tab === 'contracts'" x-transition>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm text-gray-500">{{ $employee->contracts->count() }} contract(s) on file</p>
                        <button onclick="document.getElementById('addContractForm').classList.toggle('hidden')" class="text-xs font-medium text-indigo-600 hover:underline flex items-center gap-1">
                            <i class="ph ph-plus"></i> Add Contract
                        </button>
                    </div>

                    <div id="addContractForm" class="hidden mb-4 p-5 border border-indigo-200 rounded-xl bg-indigo-50">
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

                    <div class="space-y-2">
                        @forelse($employee->contracts as $contract)
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                                        <i class="ph ph-file-text"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 capitalize">{{ $contract->type }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $contract->start_date->format('M d, Y') }}
                                            @if($contract->end_date) — {{ $contract->end_date->format('M d, Y') }} @endif
                                            @if($contract->salary) &bull; ${{ number_format($contract->salary, 2) }} @endif
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
                            <div class="text-center py-8 text-sm text-gray-400">No contracts yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB: Skills & Resume -->
                <div x-show="tab === 'skills'" x-transition>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="ph ph-lightbulb text-indigo-500"></i> Skills</h4>
                            @if($employee->skills && $employee->skills->count())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($employee->skills as $skill)
                                        <span class="px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100">{{ $skill->skill?->name ?? 'Unknown' }} @if($skill->skillLevel) ({{ $skill->skillLevel->name }}) @endif</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400">No skills recorded.</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="ph ph-list-dashes text-indigo-500"></i> Resume / Experience</h4>
                            @if($employee->resumeLines && $employee->resumeLines->count())
                                <div class="space-y-3">
                                    @foreach($employee->resumeLines as $line)
                                        <div class="border-l-2 border-indigo-200 pl-3">
                                            <p class="text-sm font-medium text-gray-800">{{ $line->name }}</p>
                                            <p class="text-xs text-gray-500">@if($line->lineType) {{ $line->lineType->name }} @endif @if($line->date_start) &bull; {{ $line->date_start->format('M Y') }} @if($line->date_end) - {{ $line->date_end->format('M Y') }} @else - Present @endif @endif</p>
                                            @if($line->description)
                                                <p class="text-xs text-gray-600 mt-0.5">{{ $line->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400">No resume entries yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB: Documents -->
                <div x-show="tab === 'documents'" x-transition>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm text-gray-500">{{ $employee->documents->count() }} document(s)</p>
                        <button onclick="document.getElementById('addDocForm').classList.toggle('hidden')" class="text-xs font-medium text-indigo-600 hover:underline flex items-center gap-1">
                            <i class="ph ph-plus"></i> Upload Document
                        </button>
                    </div>

                    <div id="addDocForm" class="hidden mb-4 p-5 border border-indigo-200 rounded-xl bg-indigo-50">
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

                    <div class="space-y-2">
                        @forelse($employee->documents as $doc)
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
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
                            <div class="text-center py-8 text-sm text-gray-400">No documents uploaded yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB: Attendance -->
                <div x-show="tab === 'attendance'" x-transition>
                    <div class="text-center py-10">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-400 flex items-center justify-center mx-auto mb-4">
                            <i class="ph ph-clock text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Attendance Overview</h3>
                        <p class="text-sm text-gray-500 mb-6">View and manage attendance records for this employee.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-lg mx-auto mb-6">
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-green-700">0</p>
                                <p class="text-xs text-green-600 mt-1">Present (This Month)</p>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-yellow-700">0</p>
                                <p class="text-xs text-yellow-600 mt-1">Late</p>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-red-700">0</p>
                                <p class="text-xs text-red-600 mt-1">Absent</p>
                            </div>
                        </div>
                        <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                            <i class="ph ph-arrow-right"></i> View Full Attendance
                        </a>
                    </div>
                </div>

                <!-- TAB: Leave -->
                <div x-show="tab === 'leave'" x-transition>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="ph ph-coins text-indigo-500"></i> Leave Allocations</h4>
                            @if($employee->user?->leaveAllocations && $employee->user->leaveAllocations->count())
                                <div class="space-y-2">
                                    @foreach($employee->user->leaveAllocations as $allocation)
                                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-800">{{ $allocation->leaveType?->name ?? 'Leave' }}</span>
                                            <span class="text-sm text-gray-600">{{ $allocation->total_allocated_days ?? $allocation->allocated_days ?? 0 }} days</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400">No leave allocations yet.</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="ph ph-calendar-blank text-indigo-500"></i> Recent Leave Requests</h4>
                            @if($employee->user?->leaveRequests && $employee->user->leaveRequests->count())
                                <div class="space-y-2">
                                    @foreach($employee->user->leaveRequests->take(5) as $lr)
                                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-800">{{ $lr->leaveType?->name ?? 'Leave' }}</p>
                                                <p class="text-xs text-gray-500">{{ $lr->start_date?->format('M d') }} - {{ $lr->end_date?->format('M d, Y') }}</p>
                                            </div>
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                @if($lr->status === 'approved') bg-green-100 text-green-700
                                                @elseif($lr->status === 'rejected') bg-red-100 text-red-700
                                                @else bg-yellow-100 text-yellow-700 @endif">
                                                {{ ucfirst($lr->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400">No leave requests yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB: Payroll -->
                <div x-show="tab === 'payroll'" x-transition>
                    <div class="text-center py-10">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-400 flex items-center justify-center mx-auto mb-4">
                            <i class="ph ph-currency-dollar text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Payroll Information</h3>
                        <p class="text-sm text-gray-500 mb-6">View payslips, earnings, and deductions for this employee.</p>
                        <a href="{{ route('payroll.my') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                            <i class="ph ph-arrow-right"></i> View Payslips
                        </a>
                    </div>
                </div>

                <!-- TAB: Assets -->
                <div x-show="tab === 'assets'" x-transition>
                    <div class="text-center py-10">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-400 flex items-center justify-center mx-auto mb-4">
                            <i class="ph ph-devices text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Assigned Assets</h3>
                        <p class="text-sm text-gray-500 mb-6">Equipment and assets assigned to this employee.</p>
                        <a href="{{ route('assets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                            <i class="ph ph-arrow-right"></i> Manage Assets
                        </a>
                    </div>
                </div>

                <!-- TAB: History -->
                <div x-show="tab === 'history'" x-transition>
                    <p class="text-sm text-gray-500 mb-4">{{ $employee->histories->count() }} change(s) recorded</p>
                    <div class="space-y-0">
                        @forelse($employee->histories as $history)
                            <div class="flex items-start gap-3 px-4 py-3 border-l-2 border-indigo-200 ml-2">
                                <div class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mt-0.5 flex-shrink-0">
                                    <i class="ph ph-arrow-right"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $history->change_type) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        @if($history->old_value || $history->new_value)
                                            @foreach(($history->new_value ?? []) as $field => $newVal)
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
                            <div class="text-center py-8 text-sm text-gray-400">No history recorded yet.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.erp>

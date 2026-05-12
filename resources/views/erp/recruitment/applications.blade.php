<x-layouts.erp :title="'Applications'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Applications</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track candidate applications and progress.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('recruitment.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-briefcase"></i> Positions
            </a>
            <button onclick="document.getElementById('addAppModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Application
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\JobApplication::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">New</p>
            <p class="text-2xl font-bold text-blue-600">{{ \App\Models\JobApplication::where('status', 'new')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Screening</p>
            <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\JobApplication::where('status', 'screening')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Interview</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\JobApplication::where('status', 'interview')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Hired</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\JobApplication::where('status', 'hired')->count() }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex gap-2">
                <select id="filterStatus" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="">All Status</option>
                    <option value="new">New</option>
                    <option value="screening">Screening</option>
                    <option value="interview">Interview</option>
                    <option value="offered">Offered</option>
                    <option value="hired">Hired</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="relative w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search candidates..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Candidate</th>
                        <th class="px-5 py-4 border-b border-gray-100">Position</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Rating</th>
                        <th class="px-5 py-4 border-b border-gray-100">Applied</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($applications as $app)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $app->candidate_name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $app->candidate_email }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $app->position?->name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($app->status === 'new') bg-blue-100 text-blue-700
                                    @elseif($app->status === 'screening') bg-indigo-100 text-indigo-700
                                    @elseif($app->status === 'interview') bg-orange-100 text-orange-700
                                    @elseif($app->status === 'offered') bg-yellow-100 text-yellow-700
                                    @elseif($app->status === 'hired') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $app->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($app->rating)
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="ph {{ $i <= $app->rating ? 'ph-star-fill' : 'ph-star' }} text-sm {{ $i <= $app->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                        @endfor
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-400">{{ $app->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('recruitment.applications.show', $app) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No applications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

    {{-- Add Application Modal --}}
    <div id="addAppModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addAppModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('recruitment.applications.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">New Application</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Position</label>
                                <select name="job_position_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Position...</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Candidate Name</label>
                                <input type="text" name="candidate_name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Jane Doe">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                                <input type="email" name="candidate_email" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="jane@example.com">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Phone</label>
                                <input type="text" name="candidate_phone" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Initial Notes</label>
                                <input type="text" name="notes" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cover Letter</label>
                                <textarea name="cover_letter" rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Save Application</button>
                        <button type="button" onclick="document.getElementById('addAppModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>
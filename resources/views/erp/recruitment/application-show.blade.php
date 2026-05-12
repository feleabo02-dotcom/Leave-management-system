<x-layouts.erp :title="'Application - ' . $jobApplication->candidate_name">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('recruitment.applications') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $jobApplication->candidate_name }}</h1>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                    @if($jobApplication->status === 'new') bg-blue-100 text-blue-700
                    @elseif($jobApplication->status === 'screening') bg-indigo-100 text-indigo-700
                    @elseif($jobApplication->status === 'interview') bg-orange-100 text-orange-700
                    @elseif($jobApplication->status === 'offered') bg-yellow-100 text-yellow-700
                    @elseif($jobApplication->status === 'hired') bg-green-100 text-green-700
                    @else bg-red-100 text-red-700
                    @endif">
                    {{ $jobApplication->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">{{ $jobApplication->position?->name ?? 'No Position' }} &middot; Applied {{ $jobApplication->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            @can('recruitment.update')
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-arrows-clockwise"></i> Stage
                        <i class="ph ph-caret-down text-xs"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">
                        @foreach(['new', 'screening', 'interview', 'offered', 'hired', 'rejected'] as $s)
                            <form action="{{ route('recruitment.applications.stage', $jobApplication) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="{{ $s }}">
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition {{ $jobApplication->status === $s ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                    {{ ucfirst($s) }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Details Sidebar --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Contact Info</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $jobApplication->candidate_email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Phone</p>
                        <p class="text-sm font-medium text-gray-900">{{ $jobApplication->candidate_phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Applied For</p>
                        <p class="text-sm font-medium text-gray-900">{{ $jobApplication->position?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Reviewer</p>
                        <p class="text-sm font-medium text-gray-900">{{ $jobApplication->reviewer?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Rating</p>
                        @if($jobApplication->rating)
                            <div class="flex items-center gap-0.5 mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="ph {{ $i <= $jobApplication->rating ? 'ph-star-fill' : 'ph-star' }} text-lg {{ $i <= $jobApplication->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Not rated yet</p>
                        @endif
                        @can('recruitment.update')
                            <form action="{{ route('recruitment.applications.rate', $jobApplication) }}" method="POST" class="flex items-center gap-1 mt-2">
                                @csrf
                                <select name="rating" class="px-2 py-1 text-xs border border-gray-200 rounded-lg">
                                    <option value="">—</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ $jobApplication->rating === $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                <button type="submit" class="text-xs text-indigo-600 font-medium hover:underline">Rate</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>

            @if($jobApplication->cover_letter)
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Cover Letter</h3>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $jobApplication->cover_letter }}</p>
                </div>
            @endif

            @if($jobApplication->notes)
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Notes</h3>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $jobApplication->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Interviews --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Interviews List --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Interviews ({{ $jobApplication->interviews->count() }})</h3>
                    @can('recruitment.create')
                        <button onclick="document.getElementById('scheduleInterviewModal').classList.remove('hidden')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                            <i class="ph ph-plus"></i> Schedule Interview
                        </button>
                    @endcan
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($jobApplication->interviews as $interview)
                        <div class="px-5 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-sm font-bold text-gray-900">{{ $interview->interviewer?->name ?? '—' }}</p>
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold rounded uppercase
                                            @if($interview->status === 'scheduled') bg-blue-100 text-blue-700
                                            @elseif($interview->status === 'completed') bg-green-100 text-green-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ $interview->status }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 uppercase font-bold">{{ $interview->interview_mode }}</span>
                                    </div>
                                    @if($interview->interview_date)
                                        <p class="text-xs text-gray-500">{{ $interview->interview_date->format('M d, Y h:i A') }}</p>
                                    @endif
                                    @if($interview->feedback)
                                        <div class="mt-2 p-2 bg-gray-50 rounded border border-gray-100 text-xs text-gray-600">
                                            <p class="font-medium text-gray-700 mb-0.5">Feedback:</p>
                                            {{ $interview->feedback }}
                                        </div>
                                    @endif
                                    @if($interview->rating)
                                        <div class="flex items-center gap-0.5 mt-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="ph {{ $i <= $interview->rating ? 'ph-star-fill' : 'ph-star' }} text-xs {{ $i <= $interview->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                                @can('recruitment.update')
                                    @if($interview->status === 'scheduled')
                                        <div class="flex gap-1">
                                            <form action="{{ route('recruitment.interviews.update', $interview) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="text-xs text-green-600 font-medium hover:underline">Complete</button>
                                            </form>
                                            <form action="{{ route('recruitment.interviews.update', $interview) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="text-xs text-red-600 font-medium hover:underline">Cancel</button>
                                            </form>
                                        </div>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-12 text-center text-gray-400 text-sm">No interviews scheduled.</div>
                    @endforelse
                </div>
            </div>

            {{-- Schedule Interview Modal --}}
            <div id="scheduleInterviewModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('scheduleInterviewModal').classList.add('hidden')"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('recruitment.applications.interview', $jobApplication) }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Schedule Interview</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Interviewer</label>
                                        <select name="interviewer_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                            <option value="">Select Interviewer...</option>
                                            @foreach(\App\Models\User::whereHas('roles', fn($q) => $q->whereIn('slug', ['admin', 'super_admin', 'manager', 'hr_manager']))->get() as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date &amp; Time</label>
                                        <input type="datetime-local" name="interview_date" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mode</label>
                                        <select name="interview_mode" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                            <option value="in-person">In Person</option>
                                            <option value="video">Video Call</option>
                                            <option value="phone">Phone</option>
                                        </select>
                                    </div>
                                    <div></div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Notes</label>
                                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Schedule</button>
                                <button type="button" onclick="document.getElementById('scheduleInterviewModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>
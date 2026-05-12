<x-layouts.erp :title="'Position - ' . $jobPosition->name">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('recruitment.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $jobPosition->name }}</h1>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                    @if($jobPosition->status === 'open') bg-green-100 text-green-700
                    @elseif($jobPosition->status === 'draft') bg-gray-100 text-gray-600
                    @elseif($jobPosition->status === 'filled') bg-indigo-100 text-indigo-700
                    @else bg-red-100 text-red-700
                    @endif">
                    {{ $jobPosition->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">{{ $jobPosition->department ?? 'No Department' }} &middot; Created {{ $jobPosition->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('recruitment.applications') }}?position_id={{ $jobPosition->id }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-users"></i> Applications ({{ $jobPosition->applications->count() }})
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Details Sidebar --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Position Details</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Department</p>
                        <p class="text-sm font-medium text-gray-900">{{ $jobPosition->department ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Salary Range</p>
                        <p class="text-sm font-medium text-gray-900">
                            @if($jobPosition->salary_min || $jobPosition->salary_max)
                                {{ $jobPosition->salary_min ? '$' . number_format($jobPosition->salary_min, 0) : '—' }}
                                -
                                {{ $jobPosition->salary_max ? '$' . number_format($jobPosition->salary_max, 0) : '—' }}
                            @else
                                <span class="text-gray-400 italic">Not specified</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Hiring Manager</p>
                        <p class="text-sm font-medium text-gray-900">{{ $jobPosition->hiringManager?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Created By</p>
                        <p class="text-sm font-medium text-gray-900">{{ $jobPosition->creator?->name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            @if($jobPosition->description)
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Description</h3>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $jobPosition->description }}</p>
                </div>
            @endif

            @if($jobPosition->requirements)
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Requirements</h3>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $jobPosition->requirements }}</p>
                </div>
            @endif
        </div>

        {{-- Applications --}}
        <div class="md:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Applications ({{ $jobPosition->applications->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-4 border-b border-gray-100">Candidate</th>
                                <th class="px-5 py-4 border-b border-gray-100">Status</th>
                                <th class="px-5 py-4 border-b border-gray-100">Rating</th>
                                <th class="px-5 py-4 border-b border-gray-100">Applied</th>
                                <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($jobPosition->applications as $app)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('recruitment.applications.show', $app) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600">{{ $app->candidate_name }}</a>
                                        <p class="text-[10px] text-gray-400">{{ $app->candidate_email }}</p>
                                    </td>
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
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No applications yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>
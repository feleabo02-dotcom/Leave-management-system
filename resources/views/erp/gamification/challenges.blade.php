<x-layouts.erp :title="'Challenges'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Challenges</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create and manage time-bound challenges.</p>
        </div>
        <button onclick="document.getElementById('createChallengeModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i class="ph ph-plus"></i> Create Challenge
        </button>
    </div>

    {{-- Challenges Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Description</th>
                        <th class="px-5 py-4 border-b border-gray-100">State</th>
                        <th class="px-5 py-4 border-b border-gray-100">Period</th>
                        <th class="px-5 py-4 border-b border-gray-100">Dates</th>
                        <th class="px-5 py-4 border-b border-gray-100">Reward Badge</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($challenges as $challenge)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $challenge->name }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $challenge->description ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($challenge->state === 'draft') bg-gray-100 text-gray-600
                                    @elseif($challenge->state === 'in_progress') bg-indigo-100 text-indigo-700
                                    @elseif($challenge->state === 'done') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ str_replace('_', ' ', $challenge->state) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $challenge->period ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                @if($challenge->start_date)
                                    {{ $challenge->start_date->format('M d') }} - {{ $challenge->end_date ? $challenge->end_date->format('M d, Y') : '...' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $challenge->rewardBadge->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right">
                                @if($challenge->state === 'draft')
                                    <form action="{{ route('gamification.challenges.start', $challenge) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-indigo-600 transition p-1.5" title="Start Challenge"><i class="ph ph-play text-lg"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No challenges created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Challenge Modal --}}
    <div id="createChallengeModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('createChallengeModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('gamification.challenges.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Challenge</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Challenge Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Q2 Sales Sprint">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Describe the challenge..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Period</label>
                                <select name="period" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Period...</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reward Badge</label>
                                <select name="reward_badge_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Badge...</option>
                                    @foreach($badges as $badge)
                                        <option value="{{ $badge->id }}">{{ $badge->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Date</label>
                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Date</label>
                                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Challenge</button>
                        <button type="button" onclick="document.getElementById('createChallengeModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

<x-layouts.erp :title="'Badges'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Badges</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create and assign achievement badges.</p>
        </div>
        <button onclick="document.getElementById('createBadgeModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i class="ph ph-plus"></i> Create Badge
        </button>
    </div>

    {{-- Badge Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($badges as $badge)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition group">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-yellow-300 to-yellow-500 text-white flex items-center justify-center shadow-sm">
                            <i class="ph ph-certificate text-xl"></i>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                            @if($badge->active) bg-green-100 text-green-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ $badge->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $badge->name }}</h3>
                    @if($badge->description)
                        <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $badge->description }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-auto">
                        <span class="text-[10px] font-bold uppercase text-gray-400">
                            @if($badge->level) Level {{ $badge->level }} @else No Level @endif
                        </span>
                        <span class="text-xs text-gray-500">{{ $badge->assignments->count() }} awarded</span>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-5 py-3 bg-gray-50 flex justify-end">
                    <button onclick="openAssignBadgeModal('{{ $badge->id }}', '{{ $badge->name }}')" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                        <i class="ph ph-user-plus"></i> Assign
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-gray-200 rounded-xl shadow-sm p-12 text-center">
                <i class="ph ph-certificate text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-400 text-sm">No badges created yet.</p>
            </div>
        @endforelse
    </div>

    {{-- Create Badge Modal --}}
    <div id="createBadgeModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('createBadgeModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('gamification.badges.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Badge</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Badge Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Top Performer">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="What this badge represents..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Level</label>
                                <select name="level" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">No Level</option>
                                    <option value="1">Level 1</option>
                                    <option value="2">Level 2</option>
                                    <option value="3">Level 3</option>
                                </select>
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase pt-6">
                                    <input type="checkbox" name="active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Badge</button>
                        <button type="button" onclick="document.getElementById('createBadgeModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Assign Badge Modal --}}
    <div id="assignBadgeModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('assignBadgeModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="assignBadgeForm" method="POST" action="{{ route('gamification.badges.assign') }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Assign Badge</h3>
                        <p class="text-sm text-gray-500 mb-4" id="assignBadgeName"></p>
                        <input type="hidden" name="badge_id" id="assignBadgeId">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">User</label>
                                <select name="user_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select User...</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Comment</label>
                                <textarea name="comment" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Reason for awarding this badge..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Assign Badge</button>
                        <button type="button" onclick="document.getElementById('assignBadgeModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAssignBadgeModal(badgeId, badgeName) {
            document.getElementById('assignBadgeId').value = badgeId;
            document.getElementById('assignBadgeName').innerText = badgeName;
            document.getElementById('assignBadgeModal').classList.remove('hidden');
        }
    </script>
</x-layouts.erp>

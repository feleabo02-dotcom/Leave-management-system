<x-layouts.erp :title="'Project - ' . $project->name">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('projects.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manager: {{ $project->manager->name }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addTaskModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Task
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Task Lists --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Project Tasks</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($project->tasks as $task)
                        <div class="p-5 hover:bg-gray-50 transition flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <form action="{{ route('tasks.status', $task) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'todo' : 'done' }}">
                                    <button type="submit" class="w-5 h-5 rounded border-2 {{ $task->status === 'done' ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-200' }} flex items-center justify-center transition">
                                        @if($task->status === 'done') <i class="ph ph-check text-xs font-bold"></i> @endif
                                    </button>
                                </form>
                                <div>
                                    <p class="text-sm font-bold {{ $task->status === 'done' ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ $task->title }}</p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] text-gray-400 font-medium">Assigned to: {{ $task->assignee->name }}</span>
                                        @if($task->deadline)
                                            <span class="text-[10px] {{ $task->deadline->isPast() && $task->status !== 'done' ? 'text-red-500 font-bold' : 'text-gray-400' }} flex items-center gap-1">
                                                <i class="ph ph-calendar"></i> {{ $task->deadline->format('M d') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="openTimeLogModal('{{ $task->id }}', '{{ $task->title }}')" class="px-3 py-1 bg-white border border-gray-200 text-gray-700 rounded-lg text-[10px] font-black uppercase hover:bg-gray-50 transition">Log Hours</button>
                                <div class="relative group">
                                    <button class="p-1.5 text-gray-400 hover:text-gray-600 transition"><i class="ph ph-dots-three-vertical-bold"></i></button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 text-sm">No tasks added to this project yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar Summary --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-4">Resource Allocation</h3>
                <div class="space-y-4">
                    @php
                        $assignedHours = \App\Models\Timesheet::whereIn('task_id', $project->tasks->pluck('id'))->sum('hours');
                    @endphp
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Total Logged Hours</p>
                        <p class="text-xl font-black text-indigo-600">{{ number_format($assignedHours, 1) }}h</p>
                    </div>
                    <div class="pt-4 border-t border-gray-50">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">Team Members</p>
                        <div class="flex -space-x-2">
                            @foreach($project->tasks->pluck('assignee')->unique('id') as $assignee)
                                <div class="w-8 h-8 rounded-full border-2 border-white bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-700" title="{{ $assignee->name }}">
                                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Task Modal --}}
    <div id="addTaskModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addTaskModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.tasks.store', $project) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Project Task</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Task Title</label>
                                <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assign To</label>
                                <select name="assigned_to" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Deadline</label>
                                <input type="date" name="deadline" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Add Task</button>
                        <button type="button" onclick="document.getElementById('addTaskModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Log Time Modal --}}
    <div id="timeLogModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('timeLogModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="timeLogForm" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Log Work Hours</h3>
                        <p class="text-sm text-gray-500 mb-4" id="taskTitleLabel"></p>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                                    <input type="date" name="date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hours Spent</label>
                                    <input type="number" step="0.1" name="hours" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. 2.5">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" required rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="What did you do?"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Log Time</button>
                        <button type="button" onclick="document.getElementById('timeLogModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTimeLogModal(id, title) {
            document.getElementById('taskTitleLabel').innerText = 'Task: ' + title;
            document.getElementById('timeLogForm').action = '/tasks/' + id + '/log-time';
            document.getElementById('timeLogModal').classList.remove('hidden');
        }
    </script>
</x-layouts.erp>

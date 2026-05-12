<x-layouts.erp :title="'Projects'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage enterprise projects and collaborative tasks.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addProjectModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Create New Project
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition group relative">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                        @if($project->status === 'active') bg-green-100 text-green-700
                        @elseif($project->status === 'closed') bg-gray-100 text-gray-700
                        @else bg-orange-100 text-orange-700
                        @endif">
                        {{ $project->status }}
                    </span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $project->code }}</span>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition">{{ $project->name }}</h3>
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-700">
                        {{ strtoupper(substr($project->manager->name, 0, 1)) }}
                    </div>
                    <span class="text-xs text-gray-500">PM: {{ $project->manager->name }}</span>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-400">Project Tasks</span>
                        <span class="font-bold text-gray-900">{{ $project->tasks_count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        @php 
                            $doneTasks = $project->tasks()->where('status', 'done')->count();
                            $progress = $project->tasks_count > 0 ? ($doneTasks / $project->tasks_count) * 100 : 0;
                        @endphp
                        <div class="bg-indigo-600 h-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <a href="{{ route('projects.show', $project) }}" class="absolute inset-0"></a>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-gray-50 border-2 border-dashed border-gray-100 rounded-2xl">
                <i class="ph ph-briefcase text-4xl text-gray-300 mb-2"></i>
                <p class="text-gray-400 text-sm font-medium">No projects found. Create your first project to get started.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $projects->links() }}
    </div>

    {{-- Add Project Modal --}}
    <div id="addProjectModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addProjectModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Project</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Project Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Website Redesign">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Project Manager</label>
                                <select name="manager_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach(\App\Models\User::all() as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                                <select name="status" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Project</button>
                        <button type="button" onclick="document.getElementById('addProjectModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>

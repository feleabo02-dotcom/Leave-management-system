<x-layouts.erp :title="'Organization Chart'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Organization Chart</h1>
            <p class="text-sm text-gray-500 mt-0.5">Visualize your company's reporting structure.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-list"></i> Directory View
            </a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-download-simple"></i> Export PDF
            </button>
        </div>
    </div>

    {{-- Controls --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm mb-8 flex items-center justify-between">
        <div class="flex gap-2">
            <button class="px-3 py-1.5 text-sm font-medium bg-indigo-50 text-indigo-700 rounded-lg">Full Chart</button>
            <button class="px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg">By Department</button>
        </div>
        <div class="relative w-64">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Find employee..." class="w-full pl-9 pr-4 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>

    {{-- Visual Chart Canvas --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 overflow-x-auto overflow-y-hidden shadow-inner min-h-[600px] flex justify-center">
        
        {{-- Custom recursive blade partial for tree rendering --}}
        @php
            if (!function_exists('renderTree')) {
                function renderTree($node, $level = 0) {
                    $hasChildren = isset($node->children) && count($node->children) > 0;
                    $initials = strtoupper(substr($node->name ?? 'U', 0, 2));
                    
                    // Card Styling
                    $colorClass = match($level) {
                        0 => 'border-indigo-500 ring-2 ring-indigo-200 bg-white',
                        1 => 'border-blue-400 bg-white',
                        2 => 'border-teal-400 bg-white',
                        default => 'border-gray-300 bg-gray-50'
                    };

                    $avatarClass = match($level) {
                        0 => 'bg-indigo-600 text-white',
                        1 => 'bg-blue-100 text-blue-700',
                        2 => 'bg-teal-100 text-teal-700',
                        default => 'bg-gray-200 text-gray-700'
                    };
                    
                    $html = '<div class="flex flex-col items-center relative">';
                    
                    // The Node Card
                    $html .= '<div class="w-48 p-3 rounded-xl shadow-sm border-t-4 ' . $colorClass . ' flex flex-col items-center text-center transition hover:shadow-md relative z-10">';
                    $html .= '<div class="w-12 h-12 rounded-full ' . $avatarClass . ' flex items-center justify-center font-bold text-lg mb-2 shadow-sm">' . $initials . '</div>';
                    $html .= '<div class="font-bold text-gray-900 text-sm truncate w-full" title="' . $node->name . '">' . $node->name . '</div>';
                    $html .= '<div class="text-xs text-gray-500 truncate w-full mb-1">' . ($node->job_title ?? 'Employee') . '</div>';
                    $html .= '<div class="text-[10px] font-medium uppercase tracking-wider text-indigo-500">' . ($node->department->name ?? '') . '</div>';
                    $html .= '</div>';

                    // Children
                    if ($hasChildren) {
                        // Vertical line dropping down from parent
                        $html .= '<div class="w-px h-6 bg-gray-300"></div>';
                        
                        $html .= '<div class="flex gap-4 relative">';
                        
                        // Horizontal connector line spanning children
                        if (count($node->children) > 1) {
                            $html .= '<div class="absolute top-0 left-0 right-0 h-px bg-gray-300" style="left: 20%; right: 20%;"></div>';
                        }
                        
                        foreach ($node->children as $index => $child) {
                            $html .= '<div class="flex flex-col items-center relative flex-1">';
                            // Vertical connector dropping to child
                            $html .= '<div class="w-px h-6 bg-gray-300"></div>';
                            $html .= renderTree($child, $level + 1);
                            $html .= '</div>';
                        }
                        $html .= '</div>';
                    }
                    
                    $html .= '</div>';
                    return $html;
                }
            }
        @endphp

        <div class="org-tree-container inline-block py-4">
            @if(count($hierarchy) > 0)
                <div class="flex justify-center gap-12">
                    @foreach($hierarchy as $rootNode)
                        {!! renderTree($rootNode) !!}
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <i class="ph ph-tree text-6xl mb-4 text-gray-300"></i>
                    <p>No reporting hierarchy found. Ensure users have managers assigned.</p>
                </div>
            @endif
        </div>
    </div>
    
    <style>
        /* Ensures the horizontal lines connect exactly to the vertical lines */
        .org-tree-container > div > div > .flex > .absolute {
            width: calc(100% - 100% / {{ max(1, count($hierarchy)) }});
            left: calc(50% / {{ max(1, count($hierarchy)) }}) !important;
            right: auto !important;
        }
    </style>
</x-layouts.erp>

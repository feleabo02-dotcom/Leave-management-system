<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // 1. Search Employees (Users)
        $users = User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->take(5)
            ->get();
        
        foreach ($users as $user) {
            $results[] = [
                'type' => 'Employee',
                'title' => $user->name,
                'subtitle' => $user->email,
                'url' => route('employees.show', $user->id),
                'icon' => 'ph-user'
            ];
        }

        // 2. Search Projects
        $projects = Project::where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->take(3)
            ->get();

        foreach ($projects as $project) {
            $results[] = [
                'type' => 'Project',
                'title' => $project->name,
                'subtitle' => $project->code,
                'url' => route('projects.show', $project->id),
                'icon' => 'ph-projector-screen'
            ];
        }

        // 3. Search Products (Inventory)
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->take(3)
            ->get();

        foreach ($products as $product) {
            $results[] = [
                'type' => 'Product',
                'title' => $product->name,
                'subtitle' => $product->code,
                'url' => route('inventory.show', $product->id),
                'icon' => 'ph-package'
            ];
        }

        // 4. Search Tasks
        $tasks = Task::where('title', 'LIKE', "%{$query}%")
            ->take(3)
            ->get();

        foreach ($tasks as $task) {
            $results[] = [
                'type' => 'Task',
                'title' => $task->title,
                'subtitle' => 'Due: ' . ($task->deadline ? $task->deadline->format('M d') : 'No deadline'),
                'url' => route('projects.show', $task->project_id),
                'icon' => 'ph-check-square'
            ];
        }

        return response()->json($results);
    }
}

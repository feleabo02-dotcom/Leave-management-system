<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        if ($admin) {
            $project = Project::updateOrCreate(['code' => 'PRJ/ERP/001'], [
                'name' => 'ERP Implementation',
                'manager_id' => $admin->id,
                'status' => 'active',
            ]);

            Task::updateOrCreate(['project_id' => $project->id, 'title' => 'Sidebar Reorganization'], [
                'assigned_to' => $admin->id,
                'deadline' => now()->addDays(5),
                'status' => 'done',
            ]);

            Task::updateOrCreate(['project_id' => $project->id, 'title' => 'Module Data Seeding'], [
                'assigned_to' => $admin->id,
                'deadline' => now()->addDays(2),
                'status' => 'progress',
            ]);
        }
    }
}

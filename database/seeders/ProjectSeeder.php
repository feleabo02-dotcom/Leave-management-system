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
        $alex = User::where('email', 'alex.rivera@xobiyahr.com')->first() ?? User::first();
        $jordan = User::where('email', 'jordan.smith@xobiyahr.com')->first();
        $elena = User::where('email', 'elena.r@xobiyahr.com')->first();

        if ($alex) {
            // Project 1: ERP
            $erp = Project::updateOrCreate(['code' => 'PRJ-2026-001'], [
                'name' => 'XobiyaHR Modernization',
                'manager_id' => $alex->id,
                'status' => 'active',
            ]);

            Task::updateOrCreate(['title' => 'Redesign Sidebar Navigation'], [
                'project_id' => $erp->id,
                'assigned_to' => $alex->id,
                'deadline' => now()->addDays(3),
                'status' => 'done',
            ]);

            if ($jordan) {
                Task::updateOrCreate(['title' => 'Implement Password Visibility Toggles'], [
                    'project_id' => $erp->id,
                    'assigned_to' => $jordan->id,
                    'deadline' => now()->addDays(1),
                    'status' => 'progress',
                ]);
            }

            // Project 2: Cloud Migration
            $cloud = Project::updateOrCreate(['code' => 'PRJ-2026-002'], [
                'name' => 'Cloud Infrastructure Migration',
                'manager_id' => $alex->id,
                'status' => 'active',
            ]);

            if ($elena) {
                Task::updateOrCreate(['title' => 'Security Audit & Compliance'], [
                    'project_id' => $cloud->id,
                    'assigned_to' => $elena->id,
                    'deadline' => now()->addWeeks(2),
                    'status' => 'todo',
                ]);
            }
        }
    }
}

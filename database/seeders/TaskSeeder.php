<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::first();

        Task::create([
            'project_id' => $project->id,
            'title' => 'Primera tarea',
            'description' => 'Tarea de ejemplo',
            'status' => 'pending',
        ]);
    }
}

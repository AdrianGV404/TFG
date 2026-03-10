<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        if ($projects->isEmpty()) {
            return;
        }

        foreach ($projects as $project) {
            $taskCount = rand(50, 150);

            Task::factory()
                ->count($taskCount)
                ->make()
                ->each(function ($task) use ($project) {
                    $task->project_id = $project->id;
                    $task->tenant_id = $project->tenant_id;
                    $task->status = ['pending','in_progress','done'][rand(0,2)];
                    $task->priority = rand(0,10);
                    $task->title .= " 🚀 ñáéíóú 漢字 \" ' emojis 😎";
                    $task->description .= "\nLínea nueva, tabs\t, comillas \" ' , símbolos #$%&*(), emojis 🎉";
                    $task->save();
                });

            // Tarea especial de prueba
            Task::factory()->create([
                'project_id' => $project->id,
                'tenant_id' => $project->tenant_id,
                'title' => 'Tarea límite: ñáéíóú, 漢字, emojis 😀🎉, "comillas", \'simples\'',
                'description' => "Descripción compleja:\nSaltos de línea, tabs\t, símbolos #$%&*(), comillas \" ' y emojis 🚀",
                'status' => ['pending','in_progress','done'][rand(0,2)],
                'priority' => rand(0,10),
            ]);
        }
    }
}
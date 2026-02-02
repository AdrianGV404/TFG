<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use Database\Seeders\ProjectSeeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener solo los proyectos nuevos del ProjectSeeder
        $projectSeeder = new ProjectSeeder();
        $newProjects = $projectSeeder->run();

        foreach ($newProjects as $project) {
            // Generar entre 100 y 150 tareas aleatorias
            $taskCount = rand(100, 150);

            Task::factory()
                ->count($taskCount)
                ->create(['project_id' => $project->id])
                ->each(function ($task) {
                    $task->status = ['pending','in_progress','done'][rand(0,2)];
                    $task->priority = rand(0,10);
                    $task->save();
                });

            // Crear tarea especial con caracteres
            Task::factory()->create([
                'project_id' => $project->id,
                'title' => 'Tarea con caracteres: ñáéíóú, 漢字, emoji 🙂',
                'description' => "Descripción con \"comillas\", \n saltos de línea, tabs\t y símbolos #$%&*()",
                'status' => ['pending','in_progress','done'][rand(0,2)],
                'priority' => rand(0,10),
            ]);
        }
    }
}

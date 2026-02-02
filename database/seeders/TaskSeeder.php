<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');

        $projects = Project::all();

        foreach ($projects as $project) {
            // Crear entre 1 y 6 tareas aleatorias por proyecto
            $count = rand(1, 6);

            Task::factory()
                ->count($count)
                ->create([
                    'project_id' => $project->id,
                ]);

            // Crear una tarea con caracteres especiales para pruebas
            Task::factory()->create([
                'project_id' => $project->id,
                'title' => 'Tarea con caracteres: ñáéíóú, 漢字, emoji 🙂',
                'description' => "Descripción con \"comillas\", \n saltos de línea, tabs\t y símbolos #$%&*()",
            ]);
        }
    }
}

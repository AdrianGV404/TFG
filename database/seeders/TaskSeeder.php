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
        $statuses = ['pending', 'in_progress', 'done'];
        $priorityuses = ['very_high','high', 'mid', 'low', 'very_low'];

        $projects = Project::all();

        foreach ($projects as $project) {
            // Create between 1 and 6 random tasks per project using factories
            $count = rand(1, 6);

            Task::factory()
                ->count($count)
                ->create([
                    'project_id' => $project->id,
                    'status' => $faker->randomElement($statuses),
                    'priority' => $faker->randomElement($priorityuses),
                ]);

            // Add at least one task with special characters to test edge cases
            Task::factory()->create([
                'project_id' => $project->id,
                'title' => 'Tarea con caracteres: ñáéíóú, 漢字, emoji 🙂',
                'description' => "Descripción con \"comillas\", \n saltos de línea, tabs\t y símbolos #$%&*()",
                'status' => $faker->randomElement($statuses),
                'priority' => $faker->randomElement($priorityuses),
            ]);
        }
    }
}

<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            // 'project_id' => Project::factory(), // <- eliminar
            'title' => $this->faker->sentence(4) . " 🚀 ñáéíóú 漢字",
            'description' => $this->faker->paragraph() . "\nComillas \" ' , tabs\t, emojis 😎, símbolos #$%&*()",
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'done']),
            'priority' => $this->faker->numberBetween(0, 10),
            'processed_at' => null,
            // project_id y tenant_id se asignan en TaskSeeder
        ];
    }
}
<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3) . " 🚀 ñáéíóú 漢字",
            'description' => $this->faker->paragraph() . "\nComillas \" ' , tabs\t, emojis 😎, símbolos #$%&*()",
            'status' => $this->faker->randomElement(['active', 'archived']),
        ];
    }
}
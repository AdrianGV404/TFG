<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Tenant;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    public function run(): array
    {
        $faker = Faker::create('es_ES');

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            return [];
        }

        $newProjects = [];

        // Crear 20 proyectos con tenant aleatorio
        for ($i = 0; $i < 20; $i++) {
            $project = Project::create([
                'name' => $faker->sentence(3) . " 🚀 ñáéíóú 漢字",
                'description' => $faker->paragraph() . "\nComillas \" ' , tabs\t, emojis 😎, símbolos #$%&*()",
                'status' => $faker->randomElement(['active', 'archived']),
                'tenant_id' => $tenants->random()->id,
            ]);

            $newProjects[] = $project;
        }

        return $newProjects;
    }
}
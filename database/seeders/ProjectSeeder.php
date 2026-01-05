<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');

        // A few hand-crafted projects to cover edge cases (accents, quotes, emoji, special symbols)
        $samples = [
            [
                'name' => 'Proyecto Demo',
                'description' => 'Proyecto de prueba',
                'status' => 'active',
            ],
            [
                'name' => 'Café & Código',
                'description' => 'Descripción con acentos: ñ, á, é, í, ó, ú',
                'status' => 'active',
            ],
            [
                'name' => 'Proyecto "Comillas"',
                'description' => "Descripción con \"comillas\" y 'apóstrofes'",
                'status' => 'archived',
            ],
            [
                'name' => 'Proyecto 🚀',
                'description' => 'Incluye emoji y símbolos especiales: © ® ™ — prueba',
                'status' => 'active',
            ],
            [
                'name' => 'Proyecto — Largo de Prueba',
                'description' => $faker->paragraph(),
                'status' => $faker->randomElement(['active', 'archived']),
            ],
        ];

        foreach ($samples as $p) {
            Project::create($p);
        }

        // Add some randomly generated projects for variety
        Project::factory()->count(5)->create();
    }
}

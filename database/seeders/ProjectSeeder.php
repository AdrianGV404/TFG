<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    public function run(): array
    {
        $faker = Faker::create('es_ES');

        $newProjects = [];

        // Proyectos de ejemplo (no duplicar)
        $samples = [
            [
                'name' => 'Proyecto Demo',
                'description' => 'Proyecto de prueba',
            ],
            [
                'name' => 'Café & Código',
                'description' => 'Descripción con acentos: ñ, á, é, í, ó, ú',
            ],
            [
                'name' => 'Proyecto "Comillas"',
                'description' => "Descripción con \"comillas\" y 'apóstrofes'",
            ],
            [
                'name' => 'Proyecto 🚀',
                'description' => 'Incluye emoji y símbolos especiales: © ® ™ — prueba',
            ],
        ];

        foreach ($samples as $p) {
            $project = Project::firstOrCreate(
                ['name' => $p['name']],
                ['description' => $p['description'], 'status' => 'archived']
            );

            // Solo si se creó nuevo, lo añadimos al array
            if ($project->wasRecentlyCreated) {
                $newProjects[] = $project;
            }
        }

        // Crear 5 proyectos aleatorios nuevos, todos archivados
        $randomProjects = Project::factory()->count(5)->create(['status' => 'archived']);
        $newProjects = array_merge($newProjects, $randomProjects->all());

        // Devolver proyectos nuevos para TaskSeeder
        return $newProjects;
    }
}

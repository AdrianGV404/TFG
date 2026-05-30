<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,    // 1. Crea usuarios y tenants
            ProjectSeeder::class, // 2. Crea proyectos y asigna los usuarios
            TaskSeeder::class,    // 3. Crea las 50 tareas/proyecto con tiempos y la nueva due_date
        ]);
    }
}
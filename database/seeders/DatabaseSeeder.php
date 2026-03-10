<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,       // primero usuarios y tenants
            ProjectSeeder::class,    // luego 20 proyectos
            TaskSeeder::class,       // luego tareas (50-150 por proyecto)
        ]);
    }
}
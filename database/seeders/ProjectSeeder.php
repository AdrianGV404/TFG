<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::create([
            'name' => 'Proyecto Demo ',
            'description' => 'Proyecto de prueba',
            'status' => 'active',
        ]);
    }
}

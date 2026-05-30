<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Filtrar usuarios solo de este tenant
            $usersInTenant = User::query()->where('tenant_id', $tenant->id)->get();
            
            if ($usersInTenant->isEmpty()) continue;

            for ($i = 0; $i < 4; $i++) {
                $creator = $usersInTenant->random();

                $project = Project::create([
                    'name' => $faker->words(3, true) . " 🚀",
                    'description' => $faker->sentence,
                    'status' => 'active',
                    'tenant_id' => $tenant->id,
                    'created_by' => $creator->id,
                ]);

                // Asignar usuarios al proyecto (solo de este tenant)
                $count = min(rand(2, 5), $usersInTenant->count());
                $userIds = $usersInTenant->random($count)->pluck('id')->unique()->toArray();
                
                // Asegurar que el creador esté
                if (!in_array($creator->id, $userIds)) $userIds[] = $creator->id;

                $project->users()->attach($userIds);
            }
        }
    }
}
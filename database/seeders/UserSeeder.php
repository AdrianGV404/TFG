<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $tenants = [];
        
        // 1. Crear 5 Tenants (Usamos 'empresa' porque es lo que permite tu migración)
        for ($i = 1; $i <= 5; $i++) {
            $tenants[] = Tenant::create([
                'name' => "Empresa $i", 
                'type' => 'empresa' // <-- ESTE es el valor correcto según tu migración
            ]);
        }

        // 2. Crear Adrián (Admin del Tenant 1)
        User::create([
            'name' => 'Adrian',
            'email' => 'adrian@adrian.adrian',
            'password' => Hash::make('adrian'),
            'tenant_id' => $tenants[0]->id,
        ]);

        // 3. Crear 99 usuarios restantes distribuidos en los 5 tenants
        foreach ($tenants as $tenant) {
            for ($i = 0; $i < 20; $i++) {
                User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('1234'),
                    'tenant_id' => $tenant->id,
                ]);
            }
        }
    }
}
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
        $faker   = Faker::create();
        $tenants = [];

        // 1. Crear 5 Tenants de tipo empresa
        for ($i = 1; $i <= 5; $i++) {
            $tenants[] = Tenant::create([
                'name' => "Empresa $i",
                'type' => 'empresa',
            ]);
        }

        // 2. Adrián — único admin de toda la aplicación (Tenant 1)
        User::create([
            'name'      => 'admin',
            'email'     => 'admin@admin.admin',
            'password'  => Hash::make('admin'),
            'tenant_id' => $tenants[0]->id,
            'role'      => 'admin',   // ← único admin
        ]);

        // 3. Resto de usuarios: TODOS con role 'user'
        foreach ($tenants as $tenant) {
            for ($i = 0; $i < 20; $i++) {
                User::create([
                    'name'      => $faker->name(),
                    'email'     => $faker->unique()->safeEmail(),
                    'password'  => Hash::make('1234'),
                    'tenant_id' => $tenant->id,
                    'role'      => 'user',
                ]);
            }
        }
    }
}
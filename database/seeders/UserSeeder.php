<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usersData = [
            ['name' => 'Alice', 'email' => 'alice@mail.com'],
            ['name' => 'Bob', 'email' => 'bob@mail.com'],
            ['name' => 'Charlie', 'email' => 'charlie@mail.com'],
        ];

        foreach ($usersData as $data) {
            // Crear o usar usuario existente
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('1234'),
                ]
            );

            // Crear tenant personal si no existe
            $tenant = Tenant::firstOrCreate(
                ['name' => $data['name'] . ' (Personal)'],
                ['type' => 'personal']
            );

            // Asociar tenant al usuario
            $user->tenant_id = $tenant->id;
            $user->save();
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de roles.
     *
     * @return void
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'finance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore($role);
        }

        $this->command->info('Roles creados correctamente: admin, finance, user');
    }
}

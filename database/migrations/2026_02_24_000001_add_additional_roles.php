<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecuta la migración para agregar roles adicionales.
     *
     * @return void
     */
    public function up(): void
    {
        // Agregar rol de usuario normal (solo lectura)
        DB::table('roles')->insertOrIgnore([
            [
                'name' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'finance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Revierte la migración.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('roles')->whereIn('name', ['user', 'finance'])->delete();
    }
};

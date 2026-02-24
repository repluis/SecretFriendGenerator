<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecuta la migración para agregar permisos a roles.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('name');
        });

        // Asignar permisos por defecto al rol admin (puede ver todo)
        DB::table('roles')->where('name', 'admin')->update([
            'permissions' => json_encode(['*']), // Asterisco significa "todo"
        ]);

        // Asignar permisos por defecto a otros roles
        DB::table('roles')->where('name', 'finance')->update([
            'permissions' => json_encode(['dashboard', 'pagos', 'recaudaciones']),
        ]);

        DB::table('roles')->where('name', 'user')->update([
            'permissions' => json_encode(['dashboard', 'juego']),
        ]);
    }

    /**
     * Revierte la migración.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};

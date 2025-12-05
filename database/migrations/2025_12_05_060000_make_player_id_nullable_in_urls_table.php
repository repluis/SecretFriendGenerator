<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            // Primero eliminar la foreign key constraint
            $table->dropForeign(['player_id']);
            
            // Hacer la columna nullable
            $table->foreignId('player_id')->nullable()->change();
            
            // Recrear la foreign key constraint con onDelete cascade
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            // Eliminar la foreign key constraint
            $table->dropForeign(['player_id']);
            
            // Hacer la columna no nullable
            $table->foreignId('player_id')->nullable(false)->change();
            
            // Recrear la foreign key constraint
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });
    }
};

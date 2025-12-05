<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_configurations', function (Blueprint $table) {
            $table->id();
            $table->integer('startgame')->default(0)->comment('Estado del juego: 0 = no iniciado, 1 = iniciado');
            $table->timestamps();
        });

        // Crear un registro por defecto con estado 0 solo si no existe
        if (DB::table('game_configurations')->count() == 0) {
            DB::table('game_configurations')->insert([
                'startgame' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_configurations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('variable')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('configurations')->insert([
            ['variable' => 'app_name',        'value' => 'FestiFondo', 'created_at' => now(), 'updated_at' => now()],
            ['variable' => 'app_description', 'value' => 'Gestión de grupo: amigo secreto y recaudaciones', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};

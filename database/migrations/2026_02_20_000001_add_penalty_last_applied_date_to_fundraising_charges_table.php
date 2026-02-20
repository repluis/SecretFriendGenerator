<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fundraising_charges', function (Blueprint $table) {
            $table->date('penalty_last_applied_date')->nullable()->after('charge_date');
        });

        // Para registros existentes que ya tienen mora acumulada, establecer la fecha
        // de última aplicación como hoy, para evitar que se dupliquen en el próximo job.
        DB::statement("
            UPDATE fundraising_charges
            SET penalty_last_applied_date = CURRENT_DATE
            WHERE penalty_amount > 0 AND is_fully_paid = false
        ");
    }

    public function down(): void
    {
        Schema::table('fundraising_charges', function (Blueprint $table) {
            $table->dropColumn('penalty_last_applied_date');
        });
    }
};

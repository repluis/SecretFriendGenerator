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
        Schema::create('fundraising_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // 'navidad', 'cumpleanos', etc.
            $table->decimal('base_amount', 8, 2)->default(1.00);
            $table->decimal('penalty_amount', 8, 2)->default(0.00);
            $table->decimal('paid_amount', 8, 2)->default(0.00);
            $table->date('charge_date'); // The 15th when this charge was created
            $table->boolean('is_fully_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_fully_paid']);
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fundraising_charges');
    }
};

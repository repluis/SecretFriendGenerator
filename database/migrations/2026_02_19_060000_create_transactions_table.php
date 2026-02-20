<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // 'credit' or 'debit'
            $table->decimal('amount', 8, 2);
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'active']);
            $table->index(['type', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

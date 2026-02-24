<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identification')->unique()->nullable()->after('name');
        });

        // Auto-generate identification for existing users: name_without_spaces_XXXX
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $base = Str::lower(Str::replace(' ', '_', $user->name));
            $random = rand(1000, 9999);
            $identification = $base . '_' . $random;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['identification' => $identification]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('identification');
        });
    }
};

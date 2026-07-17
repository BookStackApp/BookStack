<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug', 180);
        });

        $slugMap = [];
        DB::table('users')->cursor()->each(function ($user) use (&$slugMap) {
            $userSlug = Str::slug($user->name);
            while (isset($slugMap[$userSlug])) {
                $userSlug = Str::slug($user->name . Str::random(4));
            }
            $slugMap[$userSlug] = true;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['slug' => $userSlug]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

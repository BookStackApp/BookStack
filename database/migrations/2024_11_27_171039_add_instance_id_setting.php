<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')->insert([
            'setting_key' => 'instance-id',
            'value' => Str::uuid(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'type' => 'string',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('setting_key', '=', 'instance-id')->delete();
    }
};

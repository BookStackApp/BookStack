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
        $hasPages = DB::table("entities")->where('type', '=', 'page')->exists();
        $hasExistingPreference = DB::table('settings')
            ->where('setting_key', '=', 'app-editor')
            ->exists();

        if (!$hasPages && !$hasExistingPreference) {
            DB::table("settings")->insert([
                'setting_key' => 'app-editor',
                'value' => 'wysiwyg2024',
                'type' => 'string',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasPages = DB::table("entities")->where('type', '=', 'page')->exists();
        if (!$hasPages) {
            DB::table('settings')
                ->where('setting_key', '=', 'app-editor')
                ->delete();
        }
    }
};

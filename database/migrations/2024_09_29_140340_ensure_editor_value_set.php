<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure we have an "editor" value set for pages

        // Get default
        $default = DB::table('settings')
            ->where('setting_key', '=', 'app-editor')
            ->first()
            ->value ?? 'wysiwyg';
        $default = ($default === 'markdown') ? 'markdown' : 'wysiwyg';

        // We set it to 'markdown' for pages currently with markdown content
        DB::table('pages')
            ->where('editor', '=', '')
            ->where('markdown', '!=', '')
            ->update(['editor' => 'markdown']);

        // We set it to 'wysiwyg' where we have HTML but no markdown
        DB::table('pages')
            ->where('editor', '=', '')
            ->where('markdown', '=', '')
            ->where('html', '!=', '')
            ->update(['editor' => 'wysiwyg']);

        // Otherwise, where still empty, set to the current default
        DB::table('pages')
            ->where('editor', '=', '')
            ->update(['editor' => $default]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Can't reverse due to not knowing what would have been empty before
    }
};

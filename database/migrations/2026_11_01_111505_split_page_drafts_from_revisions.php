<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new page update drafts page
        Schema::create('page_update_drafts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('page_id')->index();
            $table->string('name');
            $table->longText('html');
            $table->longText('markdown');
            $table->string('summary')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->timestamps();
        });

        // Craft a query to get all existing update drafts
        $pageDraftsQuery = DB::table('page_revisions')
            ->select(['page_id', 'name', 'html', 'markdown', 'summary', 'created_by', 'created_at', 'updated_at'])
            ->where('type', '=', 'update_draft');

        // Insert them into our new table
        DB::table('page_update_drafts')->insertUsing(
            ['page_id', 'name', 'html', 'markdown', 'summary', 'created_by', 'created_at', 'updated_at'],
            $pageDraftsQuery,
        );

        // Delete existing drafts from page_revisions
        DB::table('page_revisions')
            ->where('type', '!=', 'version')
            ->delete();

        // Remove the no-longer-needed type column from the revisions table
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the type column on page revisions
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->string('type')->default('version')->index();
        });

        // Fetch all page update drafts
        $updateDraftsQuery = DB::table('page_update_drafts')->select(
            [
                'page_id', 'name', 'html', 'markdown', 'created_by', 'created_at', 'updated_at', 'summary',
                DB::raw("'' as slug"),
                DB::raw("'' as book_slug"),
                DB::raw("'update_draft' as type"),
                DB::raw("0 as revision_number"),
            ]
        );

        // Insert update drafts back into page revisions
        DB::table('page_revisions')->insertUsing(
            ['page_id', 'name', 'html', 'markdown', 'created_by', 'created_at', 'updated_at', 'summary', 'slug', 'book_slug', 'type', 'revision_number'],
            $updateDraftsQuery,
        );

        // Drop the page_update_drafts_table
        Schema::dropIfExists('page_update_drafts');
    }
};

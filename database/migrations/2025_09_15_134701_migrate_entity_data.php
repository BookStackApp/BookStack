<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Start a transaction to avoid leaving a message DB on error
        DB::beginTransaction();

        // Migrate book/shelf data to entities
        foreach (['books' => 'book', 'bookshelves' => 'bookshelf'] as $table => $type) {
            DB::table('entities')->insertUsing([
                'id', 'type', 'name', 'slug', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'owned_by',
            ], DB::table($table)->select([
                'id', DB::raw("'{$type}'"), 'name', 'slug', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'owned_by',
            ]));
        }

        // Migrate chapter data to entities
        DB::table('entities')->insertUsing([
            'id', 'type', 'name', 'slug', 'book_id', 'priority', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'owned_by',
        ], DB::table('chapters')->select([
            'id', DB::raw("'chapter'"), 'name', 'slug', 'book_id', 'priority', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'owned_by',
        ]));

        DB::table('entities')->insertUsing([
            'id', 'type', 'name', 'slug', 'book_id', 'chapter_id', 'priority', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'owned_by',
        ], DB::table('pages')->select([
            'id', DB::raw("'page'"), 'name', 'slug', 'book_id', 'chapter_id', 'priority', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'owned_by',
        ]));

        // Migrate shelf data to entity_container_data
        DB::table('entity_container_data')->insertUsing([
            'entity_id', 'entity_type', 'description', 'description_html', 'image_id',
        ], DB::table('bookshelves')->select([
            'id', DB::raw("'bookshelf'"), 'description', 'description_html', 'image_id',
        ]));

        // Migrate book data to entity_container_data
        DB::table('entity_container_data')->insertUsing([
            'entity_id', 'entity_type', 'description', 'description_html', 'default_template_id', 'image_id', 'sort_rule_id'
        ], DB::table('books')->select([
            'id', DB::raw("'book'"), 'description', 'description_html', 'default_template_id', 'image_id', 'sort_rule_id'
        ]));

        // Migrate chapter data to entity_container_data
        DB::table('entity_container_data')->insertUsing([
            'entity_id', 'entity_type', 'description', 'description_html', 'default_template_id',
        ], DB::table('chapters')->select([
            'id', DB::raw("'chapter'"), 'description', 'description_html', 'default_template_id',
        ]));

        // Migrate page data to entity_page_data
        DB::table('entity_page_data')->insertUsing([
            'page_id', 'draft', 'template', 'revision_count', 'editor', 'html', 'text', 'markdown',
        ], DB::table('pages')->select([
            'id', 'draft', 'template', 'revision_count', 'editor', 'html', 'text', 'markdown',
        ]));

        // Fix up data - Convert 0 id references to null
        DB::table('entities')->where('created_by', '=', 0)->update(['created_by' => null]);
        DB::table('entities')->where('updated_by', '=', 0)->update(['updated_by' => null]);
        DB::table('entities')->where('owned_by', '=', 0)->update(['owned_by' => null]);
        DB::table('entities')->where('chapter_id', '=', 0)->update(['chapter_id' => null]);

        // Fix up data - Convert any missing id-based references to null
        $userIdQuery = DB::table('users')->select('id');
        DB::table('entities')->whereNotIn('created_by', $userIdQuery)->update(['created_by' => null]);
        DB::table('entities')->whereNotIn('updated_by', $userIdQuery)->update(['updated_by' => null]);
        DB::table('entities')->whereNotIn('owned_by', $userIdQuery)->update(['owned_by' => null]);
        DB::table('entities')->whereNotIn('chapter_id', DB::table('chapters')->select('id'))->update(['chapter_id' => null]);

        // Commit our changes within our transaction
        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action here since the actual data remains in the database for the old tables,
        // so data reversion actions are done in a later migration when the old tables are dropped.
    }
};

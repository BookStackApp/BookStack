<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddEntityIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->index('slug');
            $table->index('created_by');
            $table->index('updated_by');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->index('slug');
            $table->index('book_id');
            $table->index('chapter_id');
            $table->index('priority');
            $table->index('created_by');
            $table->index('updated_by');
        });
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->index('page_id');
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->index('slug');
            $table->index('book_id');
            $table->index('priority');
            $table->index('created_by');
            $table->index('updated_by');
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->index('book_id');
            $table->index('user_id');
            $table->index('entity_id');
        });
        Schema::table('views', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('viewable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('books_slug_index');
            $table->dropIndex('books_created_by_index');
            $table->dropIndex('books_updated_by_index');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex('pages_slug_index');
            $table->dropIndex('pages_book_id_index');
            $table->dropIndex('pages_chapter_id_index');
            $table->dropIndex('pages_priority_index');
            $table->dropIndex('pages_created_by_index');
            $table->dropIndex('pages_updated_by_index');
        });
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->dropIndex('page_revisions_page_id_index');
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex('chapters_slug_index');
            $table->dropIndex('chapters_book_id_index');
            $table->dropIndex('chapters_priority_index');
            $table->dropIndex('chapters_created_by_index');
            $table->dropIndex('chapters_updated_by_index');
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_book_id_index');
            $table->dropIndex('activities_user_id_index');
            $table->dropIndex('activities_entity_id_index');
        });
        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('views_user_id_index');
            $table->dropIndex('views_viewable_id_index');
        });
    }
}

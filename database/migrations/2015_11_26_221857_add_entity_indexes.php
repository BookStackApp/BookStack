<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->dropIndex('slug');
            $table->dropIndex('created_by');
            $table->dropIndex('updated_by');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex('slug');
            $table->dropIndex('book_id');
            $table->dropIndex('chapter_id');
            $table->dropIndex('priority');
            $table->dropIndex('created_by');
            $table->dropIndex('updated_by');
        });
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->dropIndex('page_id');
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex('slug');
            $table->dropIndex('book_id');
            $table->dropIndex('priority');
            $table->dropIndex('created_by');
            $table->dropIndex('updated_by');
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('book_id');
            $table->dropIndex('user_id');
            $table->dropIndex('entity_id');
        });
        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('user_id');
            $table->dropIndex('entity_id');
        });
    }
}

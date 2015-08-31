<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSearchIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE pages ADD FULLTEXT search(name, text)');
        DB::statement('ALTER TABLE books ADD FULLTEXT search(name, description)');
        DB::statement('ALTER TABLE chapters ADD FULLTEXT search(name, description)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function(Blueprint $table) {
            $table->dropIndex('search');
        });
        Schema::table('books', function(Blueprint $table) {
            $table->dropIndex('search');
        });
        Schema::table('chapters', function(Blueprint $table) {
            $table->dropIndex('search');
        });
    }
}

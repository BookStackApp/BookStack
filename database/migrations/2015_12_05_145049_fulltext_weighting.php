<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FulltextWeighting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE pages ADD FULLTEXT name_search(name)');
        DB::statement('ALTER TABLE books ADD FULLTEXT name_search(name)');
        DB::statement('ALTER TABLE chapters ADD FULLTEXT name_search(name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function(Blueprint $table) {
            $table->dropIndex('name_search');
        });
        Schema::table('books', function(Blueprint $table) {
            $table->dropIndex('name_search');
        });
        Schema::table('chapters', function(Blueprint $table) {
            $table->dropIndex('name_search');
        });
    }
}

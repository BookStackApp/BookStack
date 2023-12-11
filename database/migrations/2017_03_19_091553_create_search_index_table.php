<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_terms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('term', 180);
            $table->string('entity_type', 100);
            $table->integer('entity_id');
            $table->integer('score');

            $table->index('term');
            $table->index('entity_type');
            $table->index(['entity_type', 'entity_id']);
            $table->index('score');
        });

        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $pages = $sm->listTableDetails('pages');
        $books = $sm->listTableDetails('books');
        $chapters = $sm->listTableDetails('chapters');

        if ($pages->hasIndex('search')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropIndex('search');
                $table->dropIndex('name_search');
            });
        }

        if ($books->hasIndex('search')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropIndex('search');
                $table->dropIndex('name_search');
            });
        }

        if ($chapters->hasIndex('search')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->dropIndex('search');
                $table->dropIndex('name_search');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This was removed for v0.24 since these indexes are removed anyway
        // and will cause issues for db engines that don't support such indexes.

//        $prefix = DB::getTablePrefix();
//        DB::statement("ALTER TABLE {$prefix}pages ADD FULLTEXT search(name, text)");
//        DB::statement("ALTER TABLE {$prefix}books ADD FULLTEXT search(name, description)");
//        DB::statement("ALTER TABLE {$prefix}chapters ADD FULLTEXT search(name, description)");
//        DB::statement("ALTER TABLE {$prefix}pages ADD FULLTEXT name_search(name)");
//        DB::statement("ALTER TABLE {$prefix}books ADD FULLTEXT name_search(name)");
//        DB::statement("ALTER TABLE {$prefix}chapters ADD FULLTEXT name_search(name)");

        Schema::dropIfExists('search_terms');
    }
};

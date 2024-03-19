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
    public function up()
    {
        // This was removed for v0.24 since these indexes are removed anyway
        // and will cause issues for db engines that don't support such indexes.

//        $prefix = DB::getTablePrefix();
//        DB::statement("ALTER TABLE {$prefix}pages ADD FULLTEXT search(name, text)");
//        DB::statement("ALTER TABLE {$prefix}books ADD FULLTEXT search(name, description)");
//        DB::statement("ALTER TABLE {$prefix}chapters ADD FULLTEXT search(name, description)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $prefix = DB::getTablePrefix();
        $pages = $sm->introspectTable($prefix . 'pages');
        $books = $sm->introspectTable($prefix . 'books');
        $chapters = $sm->introspectTable($prefix . 'chapters');

        if ($pages->hasIndex('search')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropIndex('search');
            });
        }

        if ($books->hasIndex('search')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropIndex('search');
            });
        }

        if ($chapters->hasIndex('search')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->dropIndex('search');
            });
        }
    }
};

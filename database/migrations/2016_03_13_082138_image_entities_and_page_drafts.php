<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImageEntitiesAndPageDrafts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('entity_type', 100);
            $table->integer('entity_id');
            $table->index(['entity_type', 'entity_id']);
        });

        Schema::table('pages', function(Blueprint $table) {
            $table->boolean('draft')->default(false);
            $table->index('draft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex(['entity_type', 'entity_id']);
            $table->dropColumn('entity_type');
            $table->dropColumn('entity_id');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('draft');
        });
    }
}

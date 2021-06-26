<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddEntityAccessControls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->integer('uploaded_to')->default(0);
            $table->index('uploaded_to');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->boolean('restricted')->default(false);
            $table->index('restricted');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->boolean('restricted')->default(false);
            $table->index('restricted');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('restricted')->default(false);
            $table->index('restricted');
        });

        Schema::create('restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('restrictable_id');
            $table->string('restrictable_type');
            $table->integer('role_id');
            $table->string('action');
            $table->index('role_id');
            $table->index('action');
            $table->index(['restrictable_id', 'restrictable_type']);
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
            $table->dropColumn('uploaded_to');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('restricted');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('restricted');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('restricted');
        });

        Schema::drop('restrictions');
    }
}

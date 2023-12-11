<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('updated_by');
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('updated_by');
        });
        Schema::table('images', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('updated_by');
        });
        Schema::table('books', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
};

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
	    $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('book_id');
            $table->integer('chapter_id');
            $table->string('name');
            $table->string('slug')->indexed();
            $table->longText('html');
            $table->longText('text');
            $table->integer('priority');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages');
    }
}

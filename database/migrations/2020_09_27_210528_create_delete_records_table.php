<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeleteRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delete_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('deleted_by');
            $table->string('deletable_type', 100);
            $table->integer('deletable_id');
            $table->timestamps();

            $table->index('deleted_by');
            $table->index('deletable_type');
            $table->index('deletable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delete_records');
    }
}

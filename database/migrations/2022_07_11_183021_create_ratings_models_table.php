<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings_models', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('experience_rating')->nullable();
            $table->integer('empathetic_rating')->nullable();
            $table->integer('doctor_attends_rating')->nullable();
            $table->integer('satisfied_doctor_rating')->nullable();
            $table->String('additional_comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings_models');
    }
}

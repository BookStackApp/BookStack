<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
      /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('page_id');
            $table->timestamps();

            $table->unique(['user_id', 'page_id']);
        });
    }
         
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_completions');
    }
};
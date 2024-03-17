<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deletions', function (Blueprint $table) {
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
     */
    public function down(): void
    {
        Schema::dropIfExists('deletions');
    }
};

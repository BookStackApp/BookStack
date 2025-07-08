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
        Schema::create('record_page_revisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('record_page_id');
            $table->string('name');
            $table->longText('html')->nullable();
            $table->longText('text')->nullable();
            $table->integer('created_by');
            $table->string('slug')->nullable();
            $table->string('record_slug')->nullable();
            $table->string('type')->nullable();
            $table->longText('markdown')->nullable();
            $table->string('summary')->nullable();
            $table->integer('revision_number')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_page_revisions');
    }
};

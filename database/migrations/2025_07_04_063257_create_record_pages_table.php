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
        Schema::create('record_pages', function (Blueprint $table) {
            $table->id();
            $table->integer('record_id');
            $table->integer('record_chapter_id');
            $table->string('name');
            $table->string('slug')->indexed();
            $table->longText('html');
            $table->longText('text');
            $table->integer('priority');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->tinyInteger('draft');
            $table->string('markdown');
            $table->integer('revision_count');
            $table->tinyInteger('template');
            $table->integer('owned_by')->unsigned()->index();
            $table->string('editor');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_pages');
    }
};

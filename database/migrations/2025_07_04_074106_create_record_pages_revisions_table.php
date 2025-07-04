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
        Schema::create('record_pages_revisions', function (Blueprint $table) {
            $table->id();
            $table->integer('record_page_id');
            $table->string('name');
            $table->text('html');
            $table->text('text');
            $table->integer('created_by');
            $table->string('slug');
            $table->string('record_slug');
            $table->string('type');
            $table->text('markdown');
            $table->string('summary');
            $table->integer('revision_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_pages_revisions');
    }
};

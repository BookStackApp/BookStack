<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up():
    {
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->unsignedInteger('last_read_position')->default(0);
            $table->timestamp('started_reading_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->unsignedInteger('total_read_time_seconds')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'page_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down():
    {
        Schema::dropIfExists('reading_progress');
    }
};

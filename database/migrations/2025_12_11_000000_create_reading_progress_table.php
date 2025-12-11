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
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('page_id');
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->unsignedInteger('scroll_position')->default(0);
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('last_read_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');

            $table->unique(['user_id', 'page_id']);
            $table->index(['user_id', 'last_read_at']);
            $table->index(['page_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_progress');
    }
};
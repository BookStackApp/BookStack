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
        // TODO - Handle compatibility with older databases that don't support vectors
        Schema::create('search_vectors', function (Blueprint $table) {
            $table->string('entity_type', 100);
            $table->integer('entity_id');
            $table->text('text');
            $table->vector('embedding');

            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_vectors');
    }
};

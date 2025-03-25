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

            $table->index(['entity_type', 'entity_id']);
        });

        $table = DB::getTablePrefix() . 'search_vectors';

        // TODO - Vector size might need to be dynamic
        DB::statement("ALTER TABLE {$table} ADD COLUMN (embedding VECTOR(1536) NOT NULL)");
        DB::statement("ALTER TABLE {$table} ADD VECTOR INDEX (embedding) DISTANCE=cosine");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_vectors');
    }
};

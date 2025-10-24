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
        Schema::table('comments', function (Blueprint $table) {
            $table->renameColumn('entity_id', 'commentable_id');
            $table->renameColumn('entity_type', 'commentable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->renameColumn('commentable_id', 'entity_id');
            $table->renameColumn('commentable_type', 'entity_type');
        });
    }
};

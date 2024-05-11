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
        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('entity_id', 'loggable_id');
            $table->renameColumn('entity_type', 'loggable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('loggable_id', 'entity_id');
            $table->renameColumn('loggable_type', 'entity_type');
        });
    }
};

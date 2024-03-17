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
        Schema::table('views', function (Blueprint $table) {
            $table->index(['updated_at'], 'views_updated_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('views_updated_at_index');
        });
    }
};

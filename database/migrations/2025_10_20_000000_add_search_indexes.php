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
        // 为search_terms表添加复合索引
        Schema::table('search_terms', function (Blueprint $table) {
            $table->index(['term', 'entity_type', 'entity_id']);
        });

        // 为entities表添加复合索引
        Schema::table('entities', function (Blueprint $table) {
            $table->index(['type', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 移除search_terms表的复合索引
        Schema::table('search_terms', function (Blueprint $table) {
            $table->dropIndex(['term', 'entity_type', 'entity_id']);
        });

        // 移除entities表的复合索引
        Schema::table('entities', function (Blueprint $table) {
            $table->dropIndex(['type', 'name']);
        });
    }
};

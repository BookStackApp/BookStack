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
        Schema::table('search_terms', function (Blueprint $table) {
            // 添加复合索引优化LIKE查询
            $table->index(['term', 'entity_type', 'entity_id'], 'idx_term_entity');
            $table->index(['entity_type', 'entity_id', 'score'], 'idx_entity_score');
            $table->index(['term', 'score'], 'idx_term_score');
        });

        // 优化entities表的查询
        Schema::table('entities', function (Blueprint $table) {
            $table->index(['type', 'name'], 'idx_type_name');
            $table->index(['type', 'book_id', 'chapter_id'], 'idx_type_book_chapter');
            $table->index(['updated_at', 'created_at'], 'idx_timestamps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_terms', function (Blueprint $table) {
            $table->dropIndex('idx_term_entity');
            $table->dropIndex('idx_entity_score');
            $table->dropIndex('idx_term_score');
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->dropIndex('idx_type_name');
            $table->dropIndex('idx_type_book_chapter');
            $table->dropIndex('idx_timestamps');
        });
    }
};

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
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->string('slug');
            $table->index('slug');
            $table->string('book_slug');
            $table->index('book_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('book_slug');
        });
    }
};

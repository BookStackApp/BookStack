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
        Schema::table('bookshelves', function (Blueprint $table) {
            $table->boolean('new_books_inherit_perms')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookshelves', function (Blueprint $table) {
            $table->dropColumn('new_books_inherit_perms');
        });
    }
};

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
            $table->softDeletes();
        });
        Schema::table('books', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookshelves', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('books', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

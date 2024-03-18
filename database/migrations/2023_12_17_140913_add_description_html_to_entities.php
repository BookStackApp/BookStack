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
        $addColumn = fn(Blueprint $table) => $table->text('description_html');

        Schema::table('books', $addColumn);
        Schema::table('chapters', $addColumn);
        Schema::table('bookshelves', $addColumn);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $removeColumn = fn(Blueprint $table) => $table->removeColumn('description_html');

        Schema::table('books', $removeColumn);
        Schema::table('chapters', $removeColumn);
        Schema::table('bookshelves', $removeColumn);
    }
};

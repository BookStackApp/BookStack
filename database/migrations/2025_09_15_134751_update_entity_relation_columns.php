<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<string, string|array<string>> $columnByTable
     */
    protected static array $columnByTable = [
        'activities' => 'loggable_id',
        'attachments' => 'uploaded_to',
        'bookshelves_books' => ['bookshelf_id', 'book_id'],
        'comments' => 'entity_id',
        'deletions' => 'deletable_id',
        'entity_permissions' => 'entity_id',
        'favourites' => 'favouritable_id',
        'images' => 'uploaded_to',
        'joint_permissions' => 'entity_id',
        'page_revisions' => 'page_id',
        'references' => ['from_id', 'to_id'],
        'search_terms' => 'entity_id',
        'tags' => 'entity_id',
        'views' => 'viewable_id',
        'watches' => 'watchable_id',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key constraints
        Schema::table('bookshelves_books', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
            $table->dropForeign(['bookshelf_id']);
        });

        // Update column types to unsigned big integers
        foreach (static::$columnByTable as $table => $column) {
            Schema::table($table, function (Blueprint $table) use ($column) {
                if (is_string($column)) {
                    $column = [$column];
                }

                foreach ($column as $col) {
                    $table->unsignedBigInteger($col)->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert columns to integers
        foreach (static::$columnByTable as $table => $column) {
            Schema::table($table, function (Blueprint $table) use ($column) {
                if (is_string($column)) {
                    $column = [$column];
                }

                foreach ($column as $col) {
                    $table->unsignedInteger($col)->change();
                }
            });
        }

        // Re-add foreign key constraints
        Schema::table('bookshelves_books', function (Blueprint $table) {
            $table->foreign('bookshelf_id')->references('id')->on('bookshelves')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }
};

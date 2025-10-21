<?php

use BookStack\Permissions\JointPermissionBuilder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
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

    protected static array $nullable = [
        'activities.loggable_id',
        'images.uploaded_to',
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
            $tableName = $table;
            Schema::table($table, function (Blueprint $table) use ($tableName, $column) {
                if (is_string($column)) {
                    $column = [$column];
                }

                foreach ($column as $col) {
                    if (in_array($tableName . '.' . $col, static::$nullable)) {
                        $table->unsignedBigInteger($col)->nullable()->change();
                    } else {
                        $table->unsignedBigInteger($col)->change();
                    }
                }
            });
        }

        // Convert image and activity zero values to null
        DB::table('images')->where('uploaded_to', '=', 0)->update(['uploaded_to' => null]);
        DB::table('activities')->where('loggable_id', '=', 0)->update(['loggable_id' => null]);

        // Clean up any orphaned gallery/drawio images to nullify their page relation
        DB::table('images')
            ->whereIn('type', ['gallery', 'drawio'])
            ->whereNotIn('uploaded_to', function (Builder $query) {
                $query->select('id')
                    ->from('entities')
                    ->where('type', '=', 'page');
            })->update(['uploaded_to' => null]);

        // Rebuild joint permissions if needed
        // This was moved here from 2023_01_24_104625_refactor_joint_permissions_storage since the changes
        // made for this release would mean our current logic would not be compatible with
        // the database changes being made. This is based on a count since any joint permissions
        // would have been truncated in the previous migration.
        if (\Illuminate\Support\Facades\DB::table('joint_permissions')->count() === 0) {
            app(JointPermissionBuilder::class)->rebuildForAll();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert image null values back to zeros
        DB::table('images')->whereNull('uploaded_to')->update(['uploaded_to' => '0']);

        // Revert columns to standard integers
        foreach (static::$columnByTable as $table => $column) {
            $tableName = $table;
            Schema::table($table, function (Blueprint $table) use ($tableName, $column) {
                if (is_string($column)) {
                    $column = [$column];
                }

                foreach ($column as $col) {
                    if ($tableName . '.' . $col === 'activities.loggable_id') {
                        $table->unsignedInteger($col)->nullable()->change();
                    } else if ($tableName . '.' . $col === 'images.uploaded_to') {
                        $table->unsignedInteger($col)->default(0)->change();
                    } else {
                        $table->unsignedInteger($col)->change();
                    }
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('books');
        Schema::dropIfExists('bookshelves');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->unsignedInteger('id', true)->primary();
            $table->integer('book_id')->index();
            $table->integer('chapter_id')->index();
            $table->string('name');
            $table->string('slug')->index();
            $table->longText('html');
            $table->longText('text');
            $table->integer('priority')->index();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable()->index();
            $table->integer('created_by')->index();
            $table->integer('updated_by')->index();

            $table->boolean('draft')->default(0)->index();
            $table->longText('markdown');
            $table->integer('revision_count');
            $table->boolean('template')->default(0)->index();
            $table->timestamp('deleted_at')->nullable();

            $table->unsignedInteger('owned_by')->index();
            $table->string('editor', 50)->default('');
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->unsignedInteger('id', true)->primary();
            $table->integer('book_id')->index();
            $table->string('slug')->index();
            $table->text('name');
            $table->text('description');
            $table->integer('priority')->index();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('created_by')->index();
            $table->integer('updated_by')->index();

            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('owned_by')->index();
            $table->text('description_html');
            $table->integer('default_template_id')->nullable();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->unsignedInteger('id', true)->primary();
            $table->string('name');
            $table->string('slug')->index();
            $table->text('description');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->integer('created_by')->index();
            $table->integer('updated_by')->index();

            $table->integer('image_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('owned_by')->index();

            $table->integer('default_template_id')->nullable();
            $table->text('description_html');
            $table->unsignedInteger('sort_rule_id')->nullable();
        });

        Schema::create('bookshelves', function (Blueprint $table) {
            $table->unsignedInteger('id', true)->primary();
            $table->string('name', 180);
            $table->string('slug', 180)->index();
            $table->text('description');

            $table->integer('created_by')->index();
            $table->integer('updated_by')->index();
            $table->integer('image_id')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unsignedInteger('owned_by')->index();
            $table->text('description_html');
        });

        DB::beginTransaction();

        // Revert nulls back to zeros
        DB::table('entities')->whereNull('created_by')->update(['created_by' => 0]);
        DB::table('entities')->whereNull('updated_by')->update(['updated_by' => 0]);
        DB::table('entities')->whereNull('owned_by')->update(['owned_by' => 0]);
        DB::table('entities')->whereNull('chapter_id')->update(['chapter_id' => 0]);

        // Restore data back into pages table
        $pageFields = [
            'id', 'book_id', 'chapter_id', 'name', 'slug', 'html', 'text', 'priority', 'created_at', 'updated_at',
            'created_by', 'updated_by', 'draft', 'markdown', 'revision_count', 'template', 'deleted_at', 'owned_by', 'editor'
        ];
        $pageQuery = DB::table('entities')->select($pageFields)
            ->leftJoin('entity_page_data', 'entities.id', '=', 'entity_page_data.page_id')
            ->where('type', '=', 'page');
        DB::table('pages')->insertUsing($pageFields, $pageQuery);

        // Restore data back into chapters table
        $containerJoinClause = function (JoinClause $join) {
            return $join->on('entities.id', '=', 'entity_container_data.entity_id')
                ->on('entities.type', '=', 'entity_container_data.entity_type');
        };
        $chapterFields = [
            'id', 'book_id', 'slug', 'name', 'description', 'priority', 'created_at', 'updated_at', 'created_by', 'updated_by',
            'deleted_at', 'owned_by', 'description_html', 'default_template_id'
        ];
        $chapterQuery = DB::table('entities')->select($chapterFields)
            ->leftJoin('entity_container_data', $containerJoinClause)
            ->where('type', '=', 'chapter');
        DB::table('chapters')->insertUsing($chapterFields, $chapterQuery);

        // Restore data back into books table
        $bookFields = [
            'id', 'name', 'slug', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'image_id',
            'deleted_at', 'owned_by', 'default_template_id', 'description_html', 'sort_rule_id'
        ];
        $bookQuery = DB::table('entities')->select($bookFields)
            ->leftJoin('entity_container_data', $containerJoinClause)
            ->where('type', '=', 'book');
        DB::table('books')->insertUsing($bookFields, $bookQuery);

        // Restore data back into bookshelves table
        $shelfFields = [
            'id', 'name', 'slug', 'description',  'created_by', 'updated_by', 'image_id', 'created_at', 'updated_at',
            'deleted_at', 'owned_by', 'description_html',
        ];
        $shelfQuery = DB::table('entities')->select($shelfFields)
            ->leftJoin('entity_container_data', $containerJoinClause)
            ->where('type', '=', 'bookshelf');
        DB::table('bookshelves')->insertUsing($shelfFields, $shelfQuery);

        DB::commit();
    }
};

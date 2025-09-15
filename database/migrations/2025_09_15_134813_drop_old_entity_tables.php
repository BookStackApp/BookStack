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

        Schema::table('chapters', function (Blueprint $table) {
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
    }
};

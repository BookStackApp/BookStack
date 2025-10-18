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
        Schema::create('entities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 10)->index();
            $table->string('name');
            $table->string('slug')->index();

            $table->unsignedBigInteger('book_id')->nullable()->index();
            $table->unsignedBigInteger('chapter_id')->nullable()->index();
            $table->unsignedInteger('priority')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable()->index();
            $table->timestamp('deleted_at')->nullable()->index();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('owned_by')->nullable()->index();

            $table->primary(['id', 'type'], 'entities_pk');
        });

        Schema::create('entity_container_data', function (Blueprint $table) {
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type', 10);
            $table->text('description');
            $table->text('description_html');

            $table->unsignedBigInteger('default_template_id')->nullable();
            $table->unsignedInteger('image_id')->nullable();
            $table->unsignedInteger('sort_rule_id')->nullable();

            $table->primary(['entity_id', 'entity_type'], 'entity_container_data_pk');
        });

        Schema::create('entity_page_data', function (Blueprint $table) {
            $table->unsignedBigInteger('page_id')->primary();

            $table->boolean('draft')->index();
            $table->boolean('template')->index();
            $table->unsignedInteger('revision_count');
            $table->string('editor', 50);

            $table->longText('html');
            $table->longText('text');
            $table->longText('markdown');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
        Schema::dropIfExists('entity_container_data');
        Schema::dropIfExists('entity_page_data');
    }
};

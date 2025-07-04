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
        Schema::create('record_chapters', function (Blueprint $table) {
            $table->id();
            $table->integer('record_id');
            $table->string('slug')->indexed();
            $table->text('name');
            $table->text('description');
            $table->text('description_html');
            $table->integer('priority');
            $table->integer('default_template_id')->nullable()->default(null);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('owned_by')->unsigned()->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_chapters');
    }
};

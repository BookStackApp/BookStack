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
        Schema::create('records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->indexed();
            $table->text('description');
            $table->text('description_html');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('image_id')->nullable()->default(null);
            $table->integer('owned_by')->unsigned()->index();
            $table->integer('default_template_id')->nullable()->default(null);
            $table->softDeletes(); // Add this line
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('records');
    }
};

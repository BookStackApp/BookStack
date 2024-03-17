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
        Schema::create('page_revisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->indexed();
            $table->string('name');
            $table->longText('html');
            $table->longText('text');
            $table->integer('created_by');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('page_revisions');
    }
};

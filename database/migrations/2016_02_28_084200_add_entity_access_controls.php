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
        Schema::table('images', function (Blueprint $table) {
            $table->integer('uploaded_to')->default(0);
            $table->index('uploaded_to');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->boolean('restricted')->default(false);
            $table->index('restricted');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->boolean('restricted')->default(false);
            $table->index('restricted');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('restricted')->default(false);
            $table->index('restricted');
        });

        Schema::create('restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('restrictable_id');
            $table->string('restrictable_type');
            $table->integer('role_id');
            $table->string('action');
            $table->index('role_id');
            $table->index('action');
            $table->index(['restrictable_id', 'restrictable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('uploaded_to');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('restricted');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('restricted');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('restricted');
        });

        Schema::drop('restrictions');
    }
};

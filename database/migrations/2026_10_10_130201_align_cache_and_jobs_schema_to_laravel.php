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
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedSmallInteger('attempts')->change();
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->string('connection')->change();
            $table->string('queue')->change();

            $table->index(['connection', 'queue', 'failed_at'], 'fail_queue_time_index');
        });

        Schema::table('cache', function (Blueprint $table) {
            $table->bigInteger('expiration')->change()->index('cache_expiration_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempts')->change();
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->dropIndex('fail_queue_time_index');

            $table->text('connection')->change();
            $table->text('queue')->change();
        });

        Schema::table('cache', function (Blueprint $table) {
            $table->dropIndex('cache_expiration_index');
            $table->integer('expiration')->change();
        });
    }
};

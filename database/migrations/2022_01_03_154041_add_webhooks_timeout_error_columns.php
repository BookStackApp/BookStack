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
        Schema::table('webhooks', function (Blueprint $table) {
            $table->unsignedInteger('timeout')->default(3);
            $table->text('last_error')->default('');
            $table->timestamp('last_called_at')->nullable();
            $table->timestamp('last_errored_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropColumn('timeout');
            $table->dropColumn('last_error');
            $table->dropColumn('last_called_at');
            $table->dropColumn('last_errored_at');
        });
    }
};

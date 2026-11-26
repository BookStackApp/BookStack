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
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->renameColumn('summary', 'changelog');
        });

        Schema::table('page_update_drafts', function (Blueprint $table) {
            $table->renameColumn('summary', 'changelog');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->renameColumn('changelog', 'summary');
        });

        Schema::table('page_update_drafts', function (Blueprint $table) {
            $table->renameColumn('changelog', 'summary');
        });
    }
};

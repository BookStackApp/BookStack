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
        // Migrate main data

        // Fix up data (zeros to nulls, missing relations to nulls, etc)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

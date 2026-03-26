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
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('uploaded_to_type', 20)->default('page')->after('uploaded_to');
            $table->index(['uploaded_to_type', 'uploaded_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropIndex('attachments_uploaded_to_type_uploaded_to_index');
            $table->dropColumn('uploaded_to_type');
        });
    }
};

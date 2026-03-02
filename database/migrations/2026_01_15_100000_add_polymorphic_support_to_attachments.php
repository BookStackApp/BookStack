<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            // Add new polymorphic columns
            $table->string('attachable_type')->nullable()->after('uploaded_to');
            $table->unsignedInteger('attachable_id')->nullable()->after('attachable_type');
        });

        // Migrate existing data - all current attachments are for pages
        DB::table('attachments')->update([
            'attachable_type' => 'BookStack\\Entities\\Models\\Page',
            'attachable_id' => DB::raw('uploaded_to'),
        ]);

        Schema::table('attachments', function (Blueprint $table) {
            // Make uploaded_to nullable for backward compatibility
            $table->integer('uploaded_to')->nullable()->change();
            
            // Add index for polymorphic relationship
            $table->index(['attachable_type', 'attachable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrate data back to uploaded_to before dropping columns
        DB::table('attachments')
            ->where('attachable_type', 'BookStack\\Entities\\Models\\Page')
            ->update(['uploaded_to' => DB::raw('attachable_id')]);

        Schema::table('attachments', function (Blueprint $table) {
            $table->dropIndex(['attachable_type', 'attachable_id']);
            $table->dropColumn(['attachable_type', 'attachable_id']);
            $table->integer('uploaded_to')->nullable(false)->change();
        });
    }
};

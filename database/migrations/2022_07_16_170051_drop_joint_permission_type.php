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
        DB::table('joint_permissions')
            ->where('action', '!=', 'view')
            ->delete();

        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->dropPrimary(['role_id', 'entity_type', 'entity_id', 'action']);
            $table->dropColumn('action');
            $table->primary(['role_id', 'entity_type', 'entity_id'], 'joint_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->string('action');
            $table->dropPrimary(['role_id', 'entity_type', 'entity_id']);
            $table->primary(['role_id', 'entity_type', 'entity_id', 'action']);
        });
    }
};

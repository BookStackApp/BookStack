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
        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->primary(['role_id', 'entity_type', 'entity_id', 'action'], 'joint_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->dropPrimary(['role_id', 'entity_type', 'entity_id', 'action']);
        });

        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
        });
    }
};

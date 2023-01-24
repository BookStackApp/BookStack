<?php

use BookStack\Auth\Permissions\JointPermissionBuilder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RefactorJointPermissionsStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Truncate before schema changes to avoid performance issues
        // since we'll need to rebuild anyway.
        DB::table('joint_permissions')->truncate();

        if (Schema::hasColumn('joint_permissions', 'owned_by')) {
            Schema::table('joint_permissions', function (Blueprint $table) {
                $table->dropColumn(['has_permission', 'has_permission_own', 'owned_by']);

                $table->unsignedTinyInteger('status')->index();
                $table->unsignedInteger('owner_id')->nullable()->index();
            });
        }

        // Rebuild permissions
        app(JointPermissionBuilder::class)->rebuildForAll();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('joint_permissions')->truncate();

        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->dropColumn(['status', 'owner_id']);

            $table->boolean('has_permission')->index();
            $table->boolean('has_permission_own')->index();
            $table->unsignedInteger('owned_by')->index();
        });
    }
}

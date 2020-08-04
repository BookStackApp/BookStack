<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropJointPermissionsId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->primary(['role_id', 'entity_type', 'entity_id', 'action'], 'joint_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->dropPrimary(['role_id', 'entity_type', 'entity_id', 'action']);
        });

        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
        });
    }
}

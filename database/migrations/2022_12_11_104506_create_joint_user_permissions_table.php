<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJointUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joint_user_permissions', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('entity_type');
            $table->unsignedInteger('entity_id');
            $table->boolean('has_permission')->index();

            $table->primary(['user_id', 'entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('joint_user_permissions');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollapsedRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TODO - Drop joint permissions
        // TODO - Run collapsed table rebuild.

        Schema::create('entity_permissions_collapsed', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('role_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('entity_type');
            $table->unsignedInteger('entity_id');
            $table->boolean('view')->index();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_permissions_collapsed');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->string('action');
            $table->boolean('has_permission')->default(false);
            $table->boolean('has_permission_own')->default(false);
            $table->integer('created_by');
            $table->index(['entity_id', 'entity_type']);
            $table->index('role_id');
            $table->index('action');
            $table->index('created_by');
        });

        $restrictionService = app(\BookStack\Services\RestrictionService::class);
        $restrictionService->buildEntityPermissions();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('entity_permissions');
    }
}

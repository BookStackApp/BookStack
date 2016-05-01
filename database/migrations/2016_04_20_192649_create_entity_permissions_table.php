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
            // Create indexes
            $table->index(['entity_id', 'entity_type']);
            $table->index('has_permission');
            $table->index('has_permission_own');
            $table->index('role_id');
            $table->index('action');
            $table->index('created_by');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('system_name');
            $table->boolean('hidden')->default(false);
            $table->index('hidden');
            $table->index('system_name');
        });

        // Create the new public role
        $publicRole = new \BookStack\Role();
        $publicRole->name = 'public';
        $publicRole->display_name = 'Public';
        $publicRole->description = 'The role given to public visitors if allowed';
        $publicRole->system_name = 'public';
        $publicRole->hidden = true;
        // Ensure unique name
        while (\BookStack\Role::getRole($publicRole->name) !== null) {
            $publicRole->name = $publicRole->name . str_random(2);
        }
        $publicRole->save();

        // Add new view permissions to public role
        $entities = ['Book', 'Page', 'Chapter'];
        $ops = ['View All', 'View Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $name = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
                $permission = \BookStack\Permission::getByName($name);
                // Assign view permissions to public
                $publicRole->attachPermission($permission);
            }
        }

        // Update admin role with system name
        $admin = \BookStack\Role::getRole('admin');
        $admin->system_name = 'admin';
        $admin->save();

        // Generate the new entity permissions
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

        // Delete the public role
        $public = \BookStack\Role::getSystemRole('public');
        $public->delete();

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('system_name');
        });
    }
}

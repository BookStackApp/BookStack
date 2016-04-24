<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddViewPermissionsToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $currentRoles = \BookStack\Role::all();

        // Create new view permissions
        $entities = ['Book', 'Page', 'Chapter'];
        $ops = ['View All', 'View Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $newPermission = new \BookStack\Permission();
                $newPermission->name = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
                $newPermission->display_name = $op . ' ' . $entity . 's';
                $newPermission->save();
                // Assign view permissions to all current roles
                foreach ($currentRoles as $role) {
                    $role->attachPermission($newPermission);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete the new view permissions
        $entities = ['Book', 'Page', 'Chapter'];
        $ops = ['View All', 'View Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionName = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
                $newPermission = \BookStack\Permission::where('name', '=', $permissionName)->first();
                foreach ($newPermission->roles as $role) {
                    $role->detachPermission($newPermission);
                }
                $newPermission->delete();
            }
        }
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePermissionsAndRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get roles with permissions we need to change
        $adminRole = \BookStack\Role::getRole('admin');
        $editorRole = \BookStack\Role::getRole('editor');

        // Delete old permissions
        $permissions = \BookStack\Permission::all();
        $permissions->each(function ($permission) {
            $permission->delete();
        });

        // Create & attach new admin permissions
        $permissionsToCreate = [
            'settings-manage' => 'Manage Settings',
            'users-manage' => 'Manage Users',
            'user-roles-manage' => 'Manage Roles & Permissions'
        ];
        foreach ($permissionsToCreate as $name => $displayName) {
            $newPermission = new \BookStack\Permission();
            $newPermission->name = $name;
            $newPermission->display_name = $displayName;
            $newPermission->save();
            $adminRole->attachPermission($newPermission);
        }

        // Create & attach new entity permissions
        $entities = ['Book', 'Page', 'Chapter', 'Image'];
        $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $newPermission = new \BookStack\Permission();
                $newPermission->name = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
                $newPermission->display_name = $op . ' ' . $entity . 's';
                $newPermission->save();
                $adminRole->attachPermission($newPermission);
                if ($editorRole !== null) $editorRole->attachPermission($newPermission);
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
        // Get roles with permissions we need to change
        $adminRole = \BookStack\Role::getRole('admin');

        // Delete old permissions
        $permissions = \BookStack\Permission::all();
        $permissions->each(function ($permission) {
            $permission->delete();
        });

        // Create default CRUD permissions and allocate to admins and editors
        $entities = ['Book', 'Page', 'Chapter', 'Image'];
        $ops = ['Create', 'Update', 'Delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $newPermission = new \BookStack\Permission();
                $newPermission->name = strtolower($entity) . '-' . strtolower($op);
                $newPermission->display_name = $op . ' ' . $entity . 's';
                $newPermission->save();
                $adminRole->attachPermission($newPermission);
            }
        }

        // Create admin permissions
        $entities = ['Settings', 'User'];
        $ops = ['Create', 'Update', 'Delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $newPermission = new \BookStack\Permission();
                $newPermission->name = strtolower($entity) . '-' . strtolower($op);
                $newPermission->display_name = $op . ' ' . $entity;
                $newPermission->save();
                $adminRole->attachPermission($newPermission);
            }
        }
    }
}

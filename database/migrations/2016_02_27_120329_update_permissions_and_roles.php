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
        $adminRoleId = DB::table('roles')->where('name', '=', 'admin')->first()->id;
        $editorRole = DB::table('roles')->where('name', '=', 'editor')->first();

        // Delete old permissions
        $permissions = DB::table('permissions')->delete();

        // Create & attach new admin permissions
        $permissionsToCreate = [
            'settings-manage' => __('migrations.permissions.settings-manage'),
            'users-manage' => __('migrations.permissions.users-manage'),
            'user-roles-manage' => __('migrations.permissions.user-roles-manage'),
            'restrictions-manage-all' => __('migrations.permissions.restrictions-manage-all'),
            'restrictions-manage-own' => __('migrations.permissions.restrictions-manage-own'),
        ];
        foreach ($permissionsToCreate as $name => $displayName) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $name,
                'display_name' => $displayName,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);
            DB::table('permission_role')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId
            ]);
        }

        // Create & attach new entity permissions
        $entities = ['book', 'page', 'chapter', 'image'];
        $ops = ['create-all', 'create-own', 'update-all', 'update-own', 'delete-all', 'delete-own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name' => $entity . '-' . $op,
                    'display_name' => __('migrations.permissions.ops.' . $op) . ' ' . __('migrations.permissions.entities.' . $entity),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                DB::table('permission_role')->insert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId
                ]);
                if ($editorRole !== null) {
                    DB::table('permission_role')->insert([
                        'role_id' => $editorRole->id,
                        'permission_id' => $permissionId
                    ]);
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
        // Get roles with permissions we need to change
        $adminRoleId = DB::table('roles')->where('name', '=', 'admin')->first()->id;

        // Delete old permissions
        $permissions = DB::table('permissions')->delete();

        // Create default CRUD permissions and allocate to admins and editors
        $entities = ['book', 'page', 'chapter', 'image'];
        $ops = ['create', 'update', 'delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name' => $entity . '-' . $op,
                    'display_name' => __('migrations.permissions.ops.' . $op) . ' ' . __('migrations.permissions.entities.' . $entity),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                DB::table('permission_role')->insert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId
                ]);
            }
        }

        // Create admin permissions
        $entities = ['settings', 'user'];
        $ops = ['create', 'update', 'delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name' => $entity . '-' . $op,
                    'display_name' => __('migrations.permissions.ops.' . $op) . ' ' . __('migrations.permissions.entities.' . $entity),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                DB::table('permission_role')->insert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId
                ]);
            }
        }
    }
}

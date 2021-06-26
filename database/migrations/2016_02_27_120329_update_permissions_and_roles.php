<?php

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
            'settings-manage'         => 'Manage Settings',
            'users-manage'            => 'Manage Users',
            'user-roles-manage'       => 'Manage Roles & Permissions',
            'restrictions-manage-all' => 'Manage All Entity Permissions',
            'restrictions-manage-own' => 'Manage Entity Permissions On Own Content',
        ];
        foreach ($permissionsToCreate as $name => $displayName) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name'         => $name,
                'display_name' => $displayName,
                'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
            ]);
            DB::table('permission_role')->insert([
                'role_id'       => $adminRoleId,
                'permission_id' => $permissionId,
            ]);
        }

        // Create & attach new entity permissions
        $entities = ['Book', 'Page', 'Chapter', 'Image'];
        $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name'         => strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op)),
                    'display_name' => $op . ' ' . $entity . 's',
                    'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
                DB::table('permission_role')->insert([
                    'role_id'       => $adminRoleId,
                    'permission_id' => $permissionId,
                ]);
                if ($editorRole !== null) {
                    DB::table('permission_role')->insert([
                        'role_id'       => $editorRole->id,
                        'permission_id' => $permissionId,
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
        $entities = ['Book', 'Page', 'Chapter', 'Image'];
        $ops = ['Create', 'Update', 'Delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name'         => strtolower($entity) . '-' . strtolower($op),
                    'display_name' => $op . ' ' . $entity . 's',
                    'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
                DB::table('permission_role')->insert([
                    'role_id'       => $adminRoleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        // Create admin permissions
        $entities = ['Settings', 'User'];
        $ops = ['Create', 'Update', 'Delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name'         => strtolower($entity) . '-' . strtolower($op),
                    'display_name' => $op . ' ' . $entity,
                    'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
                DB::table('permission_role')->insert([
                    'role_id'       => $adminRoleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }
}

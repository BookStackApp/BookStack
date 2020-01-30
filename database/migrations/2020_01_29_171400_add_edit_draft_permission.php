<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEditDraftPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create new editdraft permission
        $entity = 'Page';
        $p = 'EditDraft';
        $o = 'All';
        $pu = 'Update';

        // Create permission
        $permId = DB::table('role_permissions')->insertGetId([
            'name' => strtolower($entity) . '-' . strtolower($p) . '-' . strtolower($o),
            'display_name' => $p . ' ' . $o . ' ' . $entity . 's',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
        ]);

        // Find all current roles that already have update permission
        $roleIdsWithUdatePermission = DB::table('role_permissions')
            ->leftJoin('permission_role', 'role_permissions.id', '=', 'permission_role.permission_id')
            ->leftJoin('roles', 'roles.id', '=', 'permission_role.role_id')
            ->where('role_permissions.name', '=', strtolower($entity) . '-' . strtolower($pu) . '-' . strtolower($o))->get(['roles.id'])->pluck('id');

        // Generate permission_role entry
        $rowsToInsert = $roleIdsWithUdatePermission->filter(function($roleId) {
            return !is_null($roleId);
        })->map(function($roleId) use ($permId) {
            return [
                'role_id' => $roleId,
                'permission_id' => $permId
            ];
        })->toArray();

        // Assign editdraft permission to roles
        DB::table('permission_role')->insert($rowsToInsert);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete the new editdraft permission
        $entity = 'Page';
        $op = 'EditDraft All';

        $permissionName = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
        $permission = DB::table('role_permissions')->where('name', '=', $permissionName)->first();
        DB::table('permission_role')->where('permission_id', '=', $permission->id)->delete();
        DB::table('role_permissions')->where('name', '=', $permissionName)->delete();
    }
}

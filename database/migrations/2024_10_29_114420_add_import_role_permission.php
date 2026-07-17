<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new content-import permission
        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'         => 'content-import',
            'display_name' => 'Import Content',
            'created_at'   => Carbon::now()->toDateTimeString(),
            'updated_at'   => Carbon::now()->toDateTimeString(),
        ]);

        // Get existing admin-level role ids
        $settingManagePermission = DB::table('role_permissions')
            ->where('name', '=', 'settings-manage')->first();

        if (!$settingManagePermission) {
            return;
        }

        $adminRoleIds = DB::table('permission_role')
            ->where('permission_id', '=', $settingManagePermission->id)
            ->pluck('role_id')->all();

        // Assign the new permission to all existing admins
        $newPermissionRoles = array_values(array_map(function ($roleId) use ($permissionId) {
            return [
                'role_id'       => $roleId,
                'permission_id' => $permissionId,
            ];
        }, $adminRoleIds));

        DB::table('permission_role')->insert($newPermissionRoles);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove content-import permission
        $importPermission = DB::table('role_permissions')
            ->where('name', '=', 'content-import')->first();

        if (!$importPermission) {
            return;
        }

        DB::table('permission_role')->where('permission_id', '=', $importPermission->id)->delete();
        DB::table('role_permissions')->where('id', '=', $importPermission->id)->delete();
    }
};

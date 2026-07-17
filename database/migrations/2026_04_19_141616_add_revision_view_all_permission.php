<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new revision-view-all permission
        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'         => 'revision-view-all',
            'created_at'   => Carbon::now()->toDateTimeString(),
            'updated_at'   => Carbon::now()->toDateTimeString(),
        ]);

        // Get ids of page view permissions
        $pageViewPermissions = DB::table('role_permissions')
            ->whereIn('name', [
                'page-view-own',
                'page-view-all',
            ])->get();

        if ($pageViewPermissions->count() === 0) {
            return;
        }

        // Get role ids which have page view permission
        $applicableRoleIds = DB::table('permission_role')
            ->whereIn('permission_id', $pageViewPermissions->pluck('id'))
            ->pluck('role_id')
            ->unique()
            ->all();

        // Assign the new permission to relevant roles
        $newPermissionRoles = array_values(array_map(function (int $roleId) use ($permissionId) {
            return [
                'role_id'       => $roleId,
                'permission_id' => $permissionId,
            ];
        }, $applicableRoleIds));

        DB::table('permission_role')->insert($newPermissionRoles);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the permission to remove
        $revisionViewPermission = DB::table('role_permissions')
            ->where('name', '=', 'revision-view-all')
            ->first();

        if (!$revisionViewPermission) {
            return;
        }

        // Remove the permission, and its use on roles, from the database
        DB::table('permission_role')->where('permission_id', '=', $revisionViewPermission->id)->delete();
        DB::table('role_permissions')->where('id', '=', $revisionViewPermission->id)->delete();
    }
};

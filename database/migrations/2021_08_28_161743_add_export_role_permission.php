<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddExportRolePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create new templates-manage permission and assign to admin role
        $roles = DB::table('roles')->get('id');
        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'         => 'content-export',
            'display_name' => 'Export Content',
            'created_at'   => Carbon::now()->toDateTimeString(),
            'updated_at'   => Carbon::now()->toDateTimeString(),
        ]);

        $permissionRoles = $roles->map(function ($role) use ($permissionId) {
            return [
                'role_id'       => $role->id,
                'permission_id' => $permissionId,
            ];
        })->values()->toArray();

        DB::table('permission_role')->insert($permissionRoles);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove content-export permission
        $contentExportPermission = DB::table('role_permissions')
            ->where('name', '=', 'content-export')->first();

        DB::table('permission_role')->where('permission_id', '=', $contentExportPermission->id)->delete();
        DB::table('role_permissions')->where('id', '=', 'content-export')->delete();
    }
}

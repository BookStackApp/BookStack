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
        // Create new content-export permission
        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'         => 'content-export',
            'display_name' => 'Export Content',
            'created_at'   => Carbon::now()->toDateTimeString(),
            'updated_at'   => Carbon::now()->toDateTimeString(),
        ]);

        $roles = DB::table('roles')->get('id');
        $permissionRoles = $roles->map(function ($role) use ($permissionId) {
            return [
                'role_id'       => $role->id,
                'permission_id' => $permissionId,
            ];
        })->values()->toArray();

        // Assign to all existing roles in the system
        DB::table('permission_role')->insert($permissionRoles);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove content-export permission
        $contentExportPermission = DB::table('role_permissions')
            ->where('name', '=', 'content-export')->first();

        DB::table('permission_role')->where('permission_id', '=', $contentExportPermission->id)->delete();
        DB::table('role_permissions')->where('id', '=', $contentExportPermission->id)->delete();
    }
};

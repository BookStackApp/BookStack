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
        // Create new content-share-manage permission
        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'       => 'content-share-manage',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);

        // Assign to admin role by default
        $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;
        DB::table('permission_role')->insert([
            'role_id' => $adminRoleId,
            'permission_id' => $permissionId,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = DB::table('role_permissions')
            ->where('name', '=', 'content-share-manage')
            ->first();

        if ($permission) {
            // Remove from permission_role table
            DB::table('permission_role')->where('permission_id', '=', $permission->id)->delete();
            // Remove the permission itself
            DB::table('role_permissions')->where('id', '=', $permission->id)->delete();
        }
    }
};

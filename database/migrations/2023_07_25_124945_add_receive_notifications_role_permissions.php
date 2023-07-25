<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create new receive-notifications permission and assign to admin role
        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'         => 'receive-notifications',
            'display_name' => 'Receive & Manage Notifications',
            'created_at'   => Carbon::now()->toDateTimeString(),
            'updated_at'   => Carbon::now()->toDateTimeString(),
        ]);

        $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;
        DB::table('permission_role')->insert([
            'role_id' => $adminRoleId,
            'permission_id' => $permissionId,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permission = DB::table('role_permissions')
            ->where('name', '=', 'receive-notifications')
            ->first();

        if ($permission) {
            DB::table('permission_role')->where([
                'permission_id' => $permission->id,
            ])->delete();
        }

        DB::table('role_permissions')
            ->where('name', '=', 'receive-notifications')
            ->delete();
    }
};

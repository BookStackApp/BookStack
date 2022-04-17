<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddEditorChangeRolePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;

        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'         => 'editor-change',
            'display_name' => 'Change page editor',
            'created_at'   => Carbon::now()->toDateTimeString(),
            'updated_at'   => Carbon::now()->toDateTimeString(),
        ]);

        DB::table('permission_role')->insert([
            'role_id'       => $adminRoleId,
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
        DB::table('role_permissions')->where('name', '=', 'editor-change')->delete();
    }
}

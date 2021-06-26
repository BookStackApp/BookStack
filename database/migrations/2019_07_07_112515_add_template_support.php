<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplateSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('template')->default(false);
            $table->index('template');
        });

        // Create new templates-manage permission and assign to admin role
        $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;
        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'         => 'templates-manage',
            'display_name' => 'Manage Page Templates',
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
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('template');
        });

        // Remove templates-manage permission
        $templatesManagePermission = DB::table('role_permissions')
            ->where('name', '=', 'templates-manage')->first();

        DB::table('permission_role')->where('permission_id', '=', $templatesManagePermission->id)->delete();
        DB::table('role_permissions')->where('name', '=', 'templates-manage')->delete();
    }
}

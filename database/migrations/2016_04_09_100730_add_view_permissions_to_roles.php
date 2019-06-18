<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddViewPermissionsToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $currentRoles = DB::table('roles')->get();

        // Create new view permission
        $entities = ['book', 'page', 'chapter'];
        $ops = ['view-all', 'view-own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permId = DB::table('permissions')->insertGetId([
                    'name' => $entity . '-' . $op,
                    'display_name' => __('migrations.permissions.ops.' . $op) . ' ' . __('migrations.permissions.entities.' . $entity),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                // Assign view permission to all current roles
                foreach ($currentRoles as $role) {
                    DB::table('permission_role')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permId
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
        // Delete the new view permission
        $entities = ['Book', 'Page', 'Chapter'];
        $ops = ['View All', 'View Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionName = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
                $permission = DB::table('permissions')->where('name', '=', $permissionName)->first();
                DB::table('permission_role')->where('permission_id', '=', $permission->id)->delete();
                DB::table('permissions')->where('name', '=', $permissionName)->delete();
            }
        }
    }
}

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
        $currentRoles = DB::table('roles')->get();

        // Create new view permission
        $entities = ['Book', 'Page', 'Chapter'];
        $ops = ['View All', 'View Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permId = DB::table('permissions')->insertGetId([
                    'name'         => strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op)),
                    'display_name' => $op . ' ' . $entity . 's',
                    'created_at'   => Carbon::now()->toDateTimeString(),
                    'updated_at'   => Carbon::now()->toDateTimeString(),
                ]);
                // Assign view permission to all current roles
                foreach ($currentRoles as $role) {
                    DB::table('permission_role')->insert([
                        'role_id'       => $role->id,
                        'permission_id' => $permId,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
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
};

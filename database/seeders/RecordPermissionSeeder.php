<?php

namespace Database\Seeders;

use BookStack\Permissions\Models\RolePermission;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecordPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('display_name', '=', 'Admin')->first()->id;
        // Create & attach new entity permissions
        $entities = ['Record', 'Record-Page', 'Record-Chapter'];
        $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own', 'View All', 'View Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionId = DB::table('role_permissions')->insertGetId([
                    'name'         => strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op)),
                    'display_name' => $op . ' ' . $entity . 's',
                    'created_at'   => Carbon::now()->toDateTimeString(),
                    'updated_at'   => Carbon::now()->toDateTimeString(),
                ]);
                DB::table('permission_role')->insert([
                    'role_id'       => $adminRoleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }
}

<?php

use Illuminate\Support\Str;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJointPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joint_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->string('action');
            $table->boolean('has_permission')->default(false);
            $table->boolean('has_permission_own')->default(false);
            $table->integer('created_by');
            // Create indexes
            $table->index(['entity_id', 'entity_type']);
            $table->index('has_permission');
            $table->index('has_permission_own');
            $table->index('role_id');
            $table->index('action');
            $table->index('created_by');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('system_name');
            $table->boolean('hidden')->default(false);
            $table->index('hidden');
            $table->index('system_name');
        });

        Schema::rename('permissions', 'role_permissions');
        Schema::rename('restrictions', 'entity_permissions');

        // Create the new public role
        $publicRoleData = [
            'name'         => 'public',
            'display_name' => 'Public',
            'description'  => 'The role given to public visitors if allowed',
            'system_name'  => 'public',
            'hidden'       => true,
            'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
        ];

        // Ensure unique name
        while (DB::table('roles')->where('name', '=', $publicRoleData['display_name'])->count() > 0) {
            $publicRoleData['display_name'] = $publicRoleData['display_name'] . Str::random(2);
        }
        $publicRoleId = DB::table('roles')->insertGetId($publicRoleData);

        // Add new view permissions to public role
        $entities = ['Book', 'Page', 'Chapter'];
        $ops = ['View All', 'View Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $name = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
                $permission = DB::table('role_permissions')->where('name', '=', $name)->first();
                // Assign view permission to public
                DB::table('permission_role')->insert([
                    'permission_id' => $permission->id,
                    'role_id'       => $publicRoleId,
                ]);
            }
        }

        // Update admin role with system name
        DB::table('roles')->where('name', '=', 'admin')->update(['system_name' => 'admin']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('joint_permissions');

        Schema::rename('role_permissions', 'permissions');
        Schema::rename('entity_permissions', 'restrictions');

        // Delete the public role
        DB::table('roles')->where('system_name', '=', 'public')->delete();

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('system_name');
            $table->dropColumn('hidden');
        });
    }
}

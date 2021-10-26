<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Much of this code has been taken from entrust,
 * a role & permission management solution for Laravel.
 *
 * Full attribution of the database Schema shown below goes to the entrust project.
 *
 * @license MIT
 * @url https://github.com/Zizaco/entrust
 */
class AddRolesAndPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->nullableTimestamps();
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        // Create table for storing permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->nullableTimestamps();
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        // Create default roles
        $adminId = DB::table('roles')->insertGetId([
            'name'         => 'admin',
            'display_name' => 'Admin',
            'description'  => 'Administrator of the whole application',
            'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
        ]);
        $editorId = DB::table('roles')->insertGetId([
            'name'         => 'editor',
            'display_name' => 'Editor',
            'description'  => 'User can edit Books, Chapters & Pages',
            'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
        ]);
        $viewerId = DB::table('roles')->insertGetId([
            'name'         => 'viewer',
            'display_name' => 'Viewer',
            'description'  => 'User can view books & their content behind authentication',
            'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        // Create default CRUD permissions and allocate to admins and editors
        $entities = ['Book', 'Page', 'Chapter', 'Image'];
        $ops = ['Create', 'Update', 'Delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $newPermId = DB::table('permissions')->insertGetId([
                    'name'         => strtolower($entity) . '-' . strtolower($op),
                    'display_name' => $op . ' ' . $entity . 's',
                    'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
                DB::table('permission_role')->insert([
                    ['permission_id' => $newPermId, 'role_id' => $adminId],
                    ['permission_id' => $newPermId, 'role_id' => $editorId],
                ]);
            }
        }

        // Create admin permissions
        $entities = ['Settings', 'User'];
        $ops = ['Create', 'Update', 'Delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $newPermId = DB::table('permissions')->insertGetId([
                    'name'         => strtolower($entity) . '-' . strtolower($op),
                    'display_name' => $op . ' ' . $entity,
                    'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
                DB::table('permission_role')->insert([
                    'permission_id' => $newPermId,
                    'role_id'       => $adminId,
                ]);
            }
        }

        // Set all current users as admins
        // (At this point only the initially create user should be an admin)
        $users = DB::table('users')->get()->all();
        foreach ($users as $user) {
            DB::table('role_user')->insert([
                'role_id' => $adminId,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permission_role');
        Schema::drop('permissions');
        Schema::drop('role_user');
        Schema::drop('roles');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Much of this code has been taken from entrust,
 * a role & permission management solution for Laravel.
 *
 * Full attribution of the database Schema shown below goes to the entrust project.
 *
 * @license MIT
 * @package Zizaco\Entrust
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
            $table->timestamps();
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
            $table->timestamps();
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
        $admin = new \Oxbow\Role();
        $admin->name = 'admin';
        $admin->display_name = 'Admin';
        $admin->description = 'Administrator of the whole application';
        $admin->save();

        $editor = new \Oxbow\Role();
        $editor->name = 'editor';
        $editor->display_name = 'Editor';
        $editor->description = 'User can edit Books, Chapters & Pages';
        $editor->save();

        $viewer = new \Oxbow\Role();
        $viewer->name = 'viewer';
        $viewer->display_name = 'Viewer';
        $viewer->description = 'User can view books & their content behind authentication';
        $viewer->save();

        // Create default CRUD permissions and allocate to admins and editors
        $entities = ['Book', 'Page', 'Chapter', 'Image'];
        $ops = ['Create', 'Update', 'Delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $newPermission = new \Oxbow\Permission();
                $newPermission->name = strtolower($entity) . '-' . strtolower($op);
                $newPermission->display_name = $op . ' ' . $entity . 's';
                $newPermission->save();
                $admin->attachPermission($newPermission);
                $editor->attachPermission($newPermission);
            }
        }

        // Create admin permissions
        $entities = ['Settings', 'User'];
        $ops = ['Create', 'Update', 'Delete'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $newPermission = new \Oxbow\Permission();
                $newPermission->name = strtolower($entity) . '-' . strtolower($op);
                $newPermission->display_name = $op . ' ' . $entity;
                $newPermission->save();
                $admin->attachPermission($newPermission);
            }
        }

        // Set all current users as admins
        // (At this point only the initially create user should be an admin)
        $users = \Oxbow\User::all();
        foreach ($users as $user) {
            $user->attachRole($admin);
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

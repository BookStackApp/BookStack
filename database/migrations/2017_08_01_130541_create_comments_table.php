<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->string('entity_type');
            $table->longText('text')->nullable();
            $table->longText('html')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('local_id')->unsigned()->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->index(['entity_id', 'entity_type']);
            $table->index(['local_id']);

            // Assign new comment permissions to admin role
            $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;
            // Create & attach new entity permissions
            $ops = ['create-all', 'create-own', 'update-all', 'update-own', 'delete-all', 'delete-own'];
            $entity = 'comment';
            foreach ($ops as $op) {
                $permissionId = DB::table('role_permissions')->insertGetId([
                    'name' => $entity . '-' . $op,
                    'display_name' => __('migrations.permissions.ops.' . $op) . ' ' . __('migrations.permissions.entities.' . $entity),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                DB::table('permission_role')->insert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId
                ]);
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
        // Delete comment role permissions
        $ops = ['create-all', 'create-own', 'update-all', 'update-own', 'delete-all', 'delete-own'];
        $entity = 'comment';
        foreach ($ops as $op) {
            $permName = $entity . '-' . $op;
            DB::table('role_permissions')->where('name', '=', $permName)->delete();
        }
    }
}

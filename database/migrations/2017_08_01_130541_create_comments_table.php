<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
            $entity = 'Comment';
            foreach ($ops as $op) {
                $permissionId = DB::table('role_permissions')->insertGetId([
                    'name'         => strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op)),
                    'display_name' => $op . ' ' . $entity . 's',
                    'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
                DB::table('permission_role')->insert([
                    'role_id'       => $adminRoleId,
                    'permission_id' => $permissionId,
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
        $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        $entity = 'Comment';
        foreach ($ops as $op) {
            $permName = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
            DB::table('role_permissions')->where('name', '=', $permName)->delete();
        }
    }
};

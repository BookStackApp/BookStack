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
        if (Schema::hasTable('comments')) {
            return;
        }
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id')->unsigned();                       
            $table->integer('page_id')->unsigned();
            $table->longText('text')->nullable();
            $table->longText('html')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->index(['page_id', 'parent_id']);
            $table->timestamps();

            // Get roles with permissions we need to change
            $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;

            // Create & attach new entity permissions
            $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
            $entity = 'Comment';
            foreach ($ops as $op) {
                $permissionId = DB::table('role_permissions')->insertGetId([
                    'name' => strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op)),
                    'display_name' => $op . ' ' . $entity . 's',
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
        // Create & attach new entity permissions
        $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        $entity = 'Comment';
        foreach ($ops as $op) {
            $permName = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
            DB::table('role_permissions')->where('name', '=', $permName)->delete();
        }
    }
}

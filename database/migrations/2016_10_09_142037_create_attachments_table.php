<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('path');
            $table->string('extension', 20);
            $table->integer('uploaded_to');

            $table->boolean('external');
            $table->integer('order');

            $table->integer('created_by');
            $table->integer('updated_by');

            $table->index('uploaded_to');
            $table->timestamps();
        });

        // Get roles with permissions we need to change
        $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;

        // Create & attach new entity permissions
        $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        $entity = 'Attachment';
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

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');

        // Create & attach new entity permissions
        $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        $entity = 'Attachment';
        foreach ($ops as $op) {
            $permName = strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op));
            DB::table('role_permissions')->where('name', '=', $permName)->delete();
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class AddApiAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Add API tokens table
        Schema::create('api_tokens', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('client_id')->unique();
            $table->string('client_secret');
            $table->integer('user_id')->unsigned()->index();
            $table->timestamp('expires_at')->index();
            $table->nullableTimestamps();
        });

        // Add access-api permission
        $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;
        $permissionId = DB::table('role_permissions')->insertGetId([
            'name' => 'access-api',
            'display_name' => 'Access system API',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        DB::table('permission_role')->insert([
            'role_id' => $adminRoleId,
            'permission_id' => $permissionId
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove API tokens table
        Schema::dropIfExists('api_tokens');

        // Remove access-api permission
        $apiAccessPermission = DB::table('role_permissions')
            ->where('name', '=', 'access-api')->first();

        DB::table('permission_role')->where('permission_id', '=', $apiAccessPermission->id)->delete();
        DB::table('role_permissions')->where('name', '=', 'access-api')->delete();
    }
}

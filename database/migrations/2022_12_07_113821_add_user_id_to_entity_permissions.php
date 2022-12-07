<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUserIdToEntityPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entity_permissions', function (Blueprint $table) {
            $table->unsignedInteger('role_id')->nullable()->default(null)->change();
            $table->unsignedInteger('user_id')->nullable()->default(null)->index();
        });

        DB::table('entity_permissions')
            ->where('role_id', '=', 0)
            ->update(['role_id' => null]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('entity_permissions')
            ->whereNull('role_id')
            ->update(['role_id' => 0]);

        DB::table('entity_permissions')
            ->whereNotNull('user_id')
            ->delete();

        Schema::table('entity_permissions', function (Blueprint $table) {
            $table->unsignedInteger('role_id')->nullable(false)->change();
            $table->dropColumn('user_id');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveHiddenRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove the hidden property from roles
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('hidden');
        });

        // Add column to mark system users
        Schema::table('users', function (Blueprint $table) {
            $table->string('system_name')->nullable()->index();
        });

        // Insert our new public system user.
        $publicUserId = DB::table('users')->insertGetId([
            'email'           => 'guest@example.com',
            'name'            => 'Guest',
            'system_name'     => 'public',
            'email_confirmed' => true,
            'created_at'      => \Carbon\Carbon::now(),
            'updated_at'      => \Carbon\Carbon::now(),
        ]);

        // Get the public role
        $publicRole = DB::table('roles')->where('system_name', '=', 'public')->first();

        // Connect the new public user to the public role
        DB::table('role_user')->insert([
            'user_id' => $publicUserId,
            'role_id' => $publicRole->id,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('hidden')->default(false);
            $table->index('hidden');
        });

        DB::table('users')->where('system_name', '=', 'public')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('system_name');
        });

        DB::table('roles')->where('system_name', '=', 'public')->update(['hidden' => true]);
    }
}

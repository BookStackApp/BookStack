<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $guestUserId = DB::table('users')
            ->where('system_name', '=', 'public')
            ->first(['id'])->id;
        $publicRoleId = DB::table('roles')
            ->where('system_name', '=', 'public')
            ->first(['id'])->id;

        // This migration deletes secondary "Guest" user role assignments
        // as a safety precaution upon upgrade since the logic is changing
        // within the release this is introduced in, which could result in wider
        // permissions being provided upon upgrade without this intervention.

        // Previously, added roles would only partially apply their permissions
        // since some permission checks would only consider the originally assigned
        // public role, and not added roles. Within this release, additional roles
        // will fully apply.
        DB::table('role_user')
            ->where('user_id', '=', $guestUserId)
            ->where('role_id', '!=', $publicRoleId)
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No structural changes to make, and we cannot know the role ids to re-assign.
    }
};

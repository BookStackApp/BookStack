<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddEditorChangeFieldAndPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the new 'editor' column to the pages table
        Schema::table('pages', function (Blueprint $table) {
            $table->string('editor', 50)->default('');
        });

        // Populate the new 'editor' column
        // We set it to 'markdown' for pages currently with markdown content
        DB::table('pages')->where('markdown', '!=', '')->update(['editor' => 'markdown']);
        // We set it to 'wysiwyg' where we have HTML but no markdown
        DB::table('pages')->where('markdown', '=', '')
            ->where('html', '!=', '')
            ->update(['editor' => 'wysiwyg']);

        // Give the admin user permission to change the editor
        $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;

        $permissionId = DB::table('role_permissions')->insertGetId([
            'name'         => 'editor-change',
            'display_name' => 'Change page editor',
            'created_at'   => Carbon::now()->toDateTimeString(),
            'updated_at'   => Carbon::now()->toDateTimeString(),
        ]);

        DB::table('permission_role')->insert([
            'role_id'       => $adminRoleId,
            'permission_id' => $permissionId,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the new column from the pages table
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('editor');
        });

        // Remove traces of the role permission
        DB::table('role_permissions')->where('name', '=', 'editor-change')->delete();
    }
}

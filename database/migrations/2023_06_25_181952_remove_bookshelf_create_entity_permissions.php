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
        DB::table('entity_permissions')
            ->where('entity_type', '=', 'bookshelf')
            ->update(['create' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No structural changes to make, and we cannot know the permissions to re-assign.
    }
};

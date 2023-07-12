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
        // Note: v23.06.2
        // Migration removed since change to remove bookshelf create permissions was reverted.
        // Create permissions were removed as incorrectly thought to be unused, but they did
        // have a use via shelf permission copy-down to books.
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

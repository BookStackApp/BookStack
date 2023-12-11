<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migration removed due to issues during live migration.
        // Instead you can run the command `artisan bookstack:db-utf8mb4`
        // which will generate out the SQL request to upgrade your DB to utf8mb4.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};

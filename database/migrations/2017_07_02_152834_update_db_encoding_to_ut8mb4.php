<?php

use Illuminate\Database\Migrations\Migration;

class UpdateDbEncodingToUt8mb4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migration removed due to issues during live migration.
        // Instead you can run the command `artisan bookstack:db-utf8mb4-syntax`
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
}

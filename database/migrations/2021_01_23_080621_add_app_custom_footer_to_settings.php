<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppCustomFooterToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            DB::table('settings')->insert(array(
                'setting_key' => 'app-custom-footer',
                'value' => 'Powered by <a href="https://www.bookstackapp.com/">BookStack</a>',
            ));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            DB::table('settings')->delete(array(
                'setting_key' => 'app-custom-footer',
            ));
        });
    }
}

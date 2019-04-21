<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetUserProfileImagesUploadedTo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('images')
            ->where('type', '=', 'user')
            ->update([
                'uploaded_to' => DB::raw('`created_by`')
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('images')
            ->where('type', '=', 'user')
            ->update([
                'uploaded_to' => 0
            ]);
    }
}

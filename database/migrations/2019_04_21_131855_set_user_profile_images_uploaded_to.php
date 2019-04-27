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

        DB::table('images')
            ->where('type', '=', 'cover')
            ->update(['type' => 'cover_book']);

        $firstBook = DB::table('books')->first(['id']);
        if ($firstBook) {
            DB::table('images')
                ->where('type', '=', 'cover_book')
                ->update(['uploaded_to' => $firstBook->id]);
        }
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

        DB::table('images')
            ->where('type', '=', 'cover_book')
            ->update(['type' => 'cover', 'uploaded_to' => 0]);
    }
}

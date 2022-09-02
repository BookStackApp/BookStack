<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixShelfCoverImageTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This updates the 'type' field for images, uploaded as shelf cover images,
        // to be cover_bookshelf instead of cover_book.
        // This does not fix their paths, since fixing that requires a more complicated operation,
        // but their path does not affect functionality at time of this fix.

        $shelfImageIds = DB::table('bookshelves')
            ->whereNotNull('image_id')
            ->pluck('image_id')
            ->values()->all();

        DB::table('images')
            ->where('type', '=', 'cover_book')
            ->whereIn('id', $shelfImageIds)
            ->update(['type' => 'cover_bookshelf']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('images')
            ->where('type', '=', 'cover_bookshelf')
            ->update(['type' => 'cover_book']);
    }
}

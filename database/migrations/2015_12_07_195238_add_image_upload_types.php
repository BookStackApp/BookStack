<?php

use BookStack\Uploads\Image;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('path', 400);
            $table->string('type')->index();
        });

        Image::all()->each(function ($image) {
            $image->path = $image->url;
            $image->type = 'gallery';
            $image->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('path');
        });
    }
};

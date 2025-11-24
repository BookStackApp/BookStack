<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the table for storing slug history
        Schema::create('slug_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sluggable_type', 10)->index();
            $table->unsignedBigInteger('sluggable_id')->index();
            $table->string('slug')->index();
            $table->string('parent_slug')->nullable()->index();
            $table->timestamps();
        });

        // Migrate in slugs from page revisions
        $revisionSlugQuery = DB::table('page_revisions')
            ->select([
                DB::raw('\'page\' as sluggable_type'),
                'page_id as sluggable_id',
                'slug',
                'book_slug as parent_slug',
                DB::raw('min(created_at) as created_at'),
                DB::raw('min(updated_at) as updated_at'),
            ])
            ->where('type', '=', 'version')
            ->groupBy(['sluggable_id', 'slug', 'parent_slug']);

        DB::table('slug_history')->insertUsing(
            ['sluggable_type', 'sluggable_id', 'slug', 'parent_slug', 'created_at', 'updated_at'],
            $revisionSlugQuery,
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slug_history');
    }
};

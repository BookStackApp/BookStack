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
        // Find duplicate per-page revision numbers
        $existingDuplicates = DB::table('page_revisions')
            ->select(['page_id', 'revision_number'])
            ->selectExpression('count(*)', 'count')
            ->groupBy('page_id', 'revision_number')
            ->having('count', '>', 1)
            ->get();

        // Get the page ids where duplicates exist
        $pageIds = $existingDuplicates->pluck('page_id')->unique()->toArray();

        // Get the revision count values from pages
        $pageRevCounts = DB::table('entity_page_data')
            ->select('page_id', 'revision_count')
            ->whereIn('page_id', $pageIds)
            ->get()
            ->pluck('revision_count', 'page_id')
            ->all();

        foreach ($pageIds as $pageId) {
            $revisions = DB::table('page_revisions')
                ->select(['id', 'page_id', 'revision_number'])
                ->where('page_id', '=', $pageId)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                ->all();

            $maxRevisionNum = $pageRevCounts[$pageId] ?? 0;
            foreach ($revisions as $revision) {
                if ($revision->revision_number > $maxRevisionNum) {
                    $maxRevisionNum = $revision->revision_number;
                }
            }

            $trackedRevisionNumbers = [];
            foreach ($revisions as $revision) {
                if (isset($trackedRevisionNumbers[$revision->revision_number])) {
                    $maxRevisionNum++;
                    DB::table('page_revisions')
                        ->where('id', '=', $revision->id)
                        ->update(['revision_number' => $maxRevisionNum]);
                    $trackedRevisionNumbers[$maxRevisionNum] = true;
                    continue;
                }

                $trackedRevisionNumbers[$revision->revision_number] = true;
            }
        }

        // Create a new unique index to enforce unique per-page revision numbers
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->unique(['page_id', 'revision_number'], 'page_revision_unique_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->dropUnique('page_revision_unique_number');
        });
    }
};

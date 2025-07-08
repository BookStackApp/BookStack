<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Models\RecordPageRevision;
use Illuminate\Database\Eloquent\Builder;

class RecordPageRevisionQueries
{
    public function start(): Builder
    {
        return RecordPageRevision::query();
    }

    public function findLatestVersionBySlugs(string $bookSlug, string $pageSlug): ?RecordPageRevision
    {
        return RecordPageRevision::query()
            ->whereHas('page', function (Builder $query) {
                $query->scopes('visible');
            })
            ->where('slug', '=', $pageSlug)
            ->where('type', '=', 'version')
            ->where('book_slug', '=', $bookSlug)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function findLatestCurrentUserDraftsForPageId(int $pageId): ?RecordPageRevision
    {
        /** @var ?RecordPageRevision $revision */
        $revision = $this->latestCurrentUserDraftsForPageId($pageId)->first();

        return $revision;
    }

    public function latestCurrentUserDraftsForPageId(int $pageId): Builder
    {
        return $this->start()
            ->where('created_by', '=', user()->id)
            ->where('type', 'update_draft')
            ->where('record_page_id', '=', $pageId)
            ->orderBy('created_at', 'desc');
    }
}

<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\PageRevision;
use Illuminate\Database\Eloquent\Builder;

class PageRevisionQueries
{
    public function start(): Builder
    {
        return PageRevision::query();
    }

    public function findLatestVersionBySlugs(string $bookSlug, string $pageSlug): ?PageRevision
    {
        return PageRevision::query()
            ->whereHas('page', function (Builder $query) {
                $query->scopes('visible');
            })
            ->where('slug', '=', $pageSlug)
            ->where('type', '=', 'version')
            ->where('book_slug', '=', $bookSlug)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function findLatestCurrentUserDraftsForPageId(int $pageId): ?PageRevision
    {
        /** @var ?PageRevision $revision */
        $revision = $this->latestCurrentUserDraftsForPageId($pageId)->first();

        return $revision;
    }

    public function latestCurrentUserDraftsForPageId(int $pageId): Builder
    {
        return $this->start()
            ->where('created_by', '=', user()->id)
            ->where('type', 'update_draft')
            ->where('page_id', '=', $pageId)
            ->orderBy('created_at', 'desc');
    }
}

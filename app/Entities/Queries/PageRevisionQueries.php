<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\PageRevision;
use Illuminate\Database\Eloquent\Builder;

class PageRevisionQueries
{
    /**
     * Start a new revision query.
     * @return Builder<PageRevision>
     */
    public function start(): Builder
    {
        return PageRevision::query();
    }

    /**
     * Get the latest revision of a page based on the provided slugs.
     */
    public function findLatestBySlugs(string $bookSlug, string $pageSlug): ?PageRevision
    {
        return $this->start()
            ->whereHas('page', function (Builder $query) {
                $query->scopes('visible');
            })
            ->where('slug', '=', $pageSlug)
            ->where('book_slug', '=', $bookSlug)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get the page revision of the given page-local number.
     */
    public function findForPageByNumberOrFail(int $pageId, int $revisionNumber): PageRevision
    {
        return $this->start()
            ->where('page_id', '=', $pageId)
            ->where('revision_number', '=', $revisionNumber)
            ->orderBy('created_at', 'desc')
            ->firstOrFail();
    }

    /**
     * Get the page revision by revision ID.
     * The use of also using page id (and filtering by type) is purposeful to ensure that the revision
     * being returned belongs to the expected page, to avoid scenarios where it may unknowingly
     * be for a different page than that which has had its access permission checked.
     */
    public function findForPageByIdOrFail(int $pageId, int $revisionId): PageRevision
    {
        return $this->start()
            ->where('id', '=', $revisionId)
            ->where('page_id', '=', $pageId)
            ->orderBy('created_at', 'desc')
            ->firstOrFail();
    }

    /**
     * Get the max existing version number from the version revisions
     * of the page of the given ID.
     */
    public function maxRevisionVersionNumberForPageId(int $pageId): int
    {
        return $this->start()
            ->where('page_id', '=', $pageId)
            ->max('revision_number') ?? 0;
    }
}

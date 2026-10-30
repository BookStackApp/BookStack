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
     * Get the latest version revision of a page based on the provided slugs.
     */
    public function findLatestVersionBySlugs(string $bookSlug, string $pageSlug): ?PageRevision
    {
        return $this->start()
            ->whereHas('page', function (Builder $query) {
                $query->scopes('visible');
            })
            ->where('slug', '=', $pageSlug)
            ->where('type', '=', 'version')
            ->where('book_slug', '=', $bookSlug)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get the page revision version of the given page-local number.
     */
    public function findVersionForPageByNumberOrFail(int $pageId, int $revisionNumber): PageRevision
    {
        return $this->start()
            ->where('page_id', '=', $pageId)
            ->where('type', '=', 'version')
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
    public function findVersionForPageByIdOrFail(int $pageId, int $revisionId): PageRevision
    {
        return $this->start()
            ->where('id', '=', $revisionId)
            ->where('page_id', '=', $pageId)
            ->where('type', '=', 'version')
            ->orderBy('created_at', 'desc')
            ->firstOrFail();
    }

    /**
     * Get the latest page edit draft for the given page belonging the logged-in user.
     */
    public function findLatestCurrentUserDraftsForPageId(int $pageId): ?PageRevision
    {
        /** @var ?PageRevision $revision */
        $revision = $this->latestCurrentUserDraftsForPageId($pageId)->first();

        return $revision;
    }

    /**
     * Get the latest page edit drafts belonging to the currently logged in user,
     * ordered by created_at.
     */
    public function latestCurrentUserDraftsForPageId(int $pageId): Builder
    {
        return $this->start()
            ->where('created_by', '=', user()->id)
            ->where('type', 'update_draft')
            ->where('page_id', '=', $pageId)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the max existing version number from the version revisions
     * of the page of the given ID.
     */
    public function maxRevisionVersionNumberForPageId(int $pageId): int
    {
        return $this->start()
            ->where('page_id', '=', $pageId)
            ->where('type', '=', 'version')
            ->max('revision_number') ?? 0;
    }

    /**
     * Check if duplicate revisions exist for the given revision number
     * within the context of the given page ID.
     * Needed as some historic scenarios could exist to cause the revision number
     * to be duplicated/reused.
     */
    public function revisionVersionNumberHasDuplicates(int $revisionNumber, int $pageId): bool
    {
        return $this->start()
            ->where('page_id', '=', $pageId)
            ->where('revision_number', '=', $revisionNumber)
            ->where('type', '=', 'version')
            ->count() > 1;
    }
}

<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\PageUpdateDraft;
use Illuminate\Database\Eloquent\Builder;

class PageUpdateDraftQueries
{
    /**
     * Start a new revision query.
     * @return Builder<PageUpdateDraft>
     */
    protected function start(): Builder
    {
        return PageUpdateDraft::query();
    }

    /**
     * Get the latest page update draft for the given page belonging the logged-in user.
     */
    public function findLatestCurrentUserDraftsForPageId(int $pageId): ?PageUpdateDraft
    {
        return $this->latestCurrentUserDraftsForPageId($pageId)->first();
    }

    /**
     * Start a query for the the latest page edit drafts belonging to the currently logged in user,
     * ordered by most recent first.
     *
     * @return Builder<PageUpdateDraft>
     */
    public function latestCurrentUserDraftsForPageId(int $pageId): Builder
    {
        return $this->start()
            ->where('created_by', '=', user()->id)
            ->where('page_id', '=', $pageId)
            ->orderBy('created_at', 'desc');
    }
}

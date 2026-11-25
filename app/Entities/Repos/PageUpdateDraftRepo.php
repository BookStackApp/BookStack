<?php

namespace BookStack\Entities\Repos;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageUpdateDraft;
use BookStack\Entities\Queries\PageUpdateDraftQueries;

class PageUpdateDraftRepo
{
    public function __construct(
        protected PageUpdateDraftQueries $queries,
    ) {
    }

    /**
     * Delete all drafts revisions, for the given page, belonging to the current user.
     */
    public function deleteForCurrentUser(Page $page): void
    {
        $this->queries->latestCurrentUserDraftsForPageId($page->id)->reorder()->delete();
    }

    /**
     * Get a new page update draft for the given page.
     * Checks for an existing draft before providing a fresh one.
     */
    public function getNewForCurrentUser(Page $page): PageUpdateDraft
    {
        $draft = $this->queries->findLatestCurrentUserDraftsForPageId($page->id);
        if ($draft) {
            return $draft;
        }

        $draft = new PageUpdateDraft();
        $draft->page_id = $page->id;
        $draft->created_by = user()->id;

        return $draft;
    }
}

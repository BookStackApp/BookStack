<?php

namespace BookStack\Entities\Repos;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Queries\PageRevisionQueries;

class RevisionRepo
{
    public function __construct(
        protected PageRevisionQueries $queries,
    ) {
    }

    /**
     * Delete all drafts revisions, for the given page, belonging to the current user.
     */
    public function deleteDraftsForCurrentUser(Page $page): void
    {
        $this->queries->latestCurrentUserDraftsForPageId($page->id)->delete();
    }

    /**
     * Get a user update_draft page revision to update for the given page.
     * Checks for an existing revisions before providing a fresh one.
     */
    public function getNewDraftForCurrentUser(Page $page): PageRevision
    {
        $draft = $this->queries->findLatestCurrentUserDraftsForPageId($page->id);

        if ($draft) {
            return $draft;
        }

        $draft = new PageRevision();
        $draft->page_id = $page->id;
        $draft->slug = $page->slug;
        $draft->book_slug = $page->book->slug;
        $draft->created_by = user()->id;
        $draft->type = 'update_draft';

        return $draft;
    }

    /**
     * Store a new revision in the system for the given page.
     */
    public function storeNewForPage(Page $page, ?string $summary = null): PageRevision
    {
        $revision = new PageRevision();

        $revision->name = $page->name;
        $revision->html = $page->html;
        $revision->markdown = $page->markdown;
        $revision->text = $page->text;
        $revision->page_id = $page->id;
        $revision->slug = $page->slug;
        $revision->book_slug = $page->book->slug;
        $revision->created_by = user()->id;
        $revision->created_at = $page->updated_at;
        $revision->type = 'version';
        $revision->summary = $summary;
        $revision->revision_number = $page->revision_count;
        $revision->save();

        $this->deleteOldRevisions($page);

        return $revision;
    }

    /**
     * Delete old revisions, for the given page, from the system.
     */
    protected function deleteOldRevisions(Page $page)
    {
        $revisionLimit = config('app.revision_limit');
        if ($revisionLimit === false) {
            return;
        }

        $revisionsToDelete = PageRevision::query()
            ->where('page_id', '=', $page->id)
            ->orderBy('created_at', 'desc')
            ->skip(intval($revisionLimit))
            ->take(10)
            ->get(['id']);

        if ($revisionsToDelete->count() > 0) {
            PageRevision::query()->whereIn('id', $revisionsToDelete->pluck('id'))->delete();
        }
    }
}

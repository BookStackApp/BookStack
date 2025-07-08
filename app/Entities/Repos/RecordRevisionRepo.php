<?php

namespace BookStack\Entities\Repos;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Models\RecordPage;
use BookStack\Entities\Models\RecordPageRevision;
use BookStack\Entities\Queries\PageRevisionQueries;
use BookStack\Entities\Queries\RecordPageRevisionQueries;

class RecordRevisionRepo
{
    public function __construct(
        protected RecordPageRevisionQueries $queries,
    ) {
    }

    /**
     * Delete all drafts revisions, for the given page, belonging to the current user.
     */
    public function deleteDraftsForCurrentUser(RecordPage $page): void
    {
        $this->queries->latestCurrentUserDraftsForPageId($page->id)->delete();
    }

    /**
     * Get a user update_draft page revision to update for the given page.
     * Checks for an existing revisions before providing a fresh one.
     */
    public function getNewDraftForCurrentUser(RecordPage $page): RecordPageRevision
    {
        $draft = $this->queries->findLatestCurrentUserDraftsForPageId($page->id);

        if ($draft) {
            return $draft;
        }

        $draft = new RecordPageRevision();
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
    public function storeNewForPage(RecordPage $page, ?string $summary = null): RecordPageRevision
    {
        $revision = new RecordPageRevision();
        
        $revision->name = $page->name;
        $revision->html = $page->html;
        $revision->markdown = $page->markdown;
        $revision->text = $page->text;
        $revision->record_page_id = $page->id;
        $revision->slug = $page->slug;
        $revision->record_slug = $page->record->slug;
        $revision->created_by = user()->id;
        $revision->created_at = $page->updated_at;
        $revision->type = 'version';
        $revision->summary = $summary;
        $revision->revision_number = $page->revision_count;
        // dd($revision->toArray(), $page->toArray());
        $revision->save();
        // dd($page->toArray());

        $this->deleteOldRevisions($page);

        return $revision;
    }

    /**
     * Delete old revisions, for the given page, from the system.
     */
    protected function deleteOldRevisions(RecordPage $page)
    {
        // dd($page);
        $revisionLimit = config('app.revision_limit');
        if ($revisionLimit === false) {
            return;
        }

        $revisionsToDelete = RecordPageRevision::query()
            ->where('record_page_id', '=', $page->id)
            ->orderBy('created_at', 'desc')
            ->skip(intval($revisionLimit))
            ->take(10)
            ->get(['id']);

        if ($revisionsToDelete->count() > 0) {
            RecordPageRevision::query()->whereIn('id', $revisionsToDelete->pluck('id'))->delete();
        }
    }
}

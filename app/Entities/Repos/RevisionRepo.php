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
        $revision->summary = $summary;

        $pageDraftMaxRevision = $this->queries->maxRevisionVersionNumberForPageId($page->id);
        $revision->revision_number = max($page->revision_count, $pageDraftMaxRevision + 1);

        $revision->save();

        $this->deleteOldRevisions($page);

        return $revision;
    }

    /**
     * Delete old revisions, for the given page, from the system.
     */
    protected function deleteOldRevisions(Page $page): void
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

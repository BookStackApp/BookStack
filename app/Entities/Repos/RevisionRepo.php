<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Queries\PageRevisionQueries;
use BookStack\Exceptions\RevisionDeleteException;
use BookStack\Facades\Activity;
use BookStack\Util\HtmlContentFilter;
use BookStack\Util\HtmlContentFilterConfig;
use Exception;
use Ssddanbrown\HtmlDiff\Diff;

class RevisionRepo
{
    public function __construct(
        protected PageRevisionQueries $queries,
    ) {
    }

    /**
     * Store a new revision in the system for the given page.
     */
    public function storeNewForPage(Page $page, ?string $changelog = null): PageRevision
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
        $revision->changelog = $changelog;

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

    /**
     * Get the changes for a revision, compared to its previous revision, as a HTML diff.
     */
    public function getRevisionChangesDiff(PageRevision $revision, PageRevision|null $priorRevision): string
    {
        $prevContent = $priorRevision->html ?? '';

        $rawDiff = Diff::excecute($prevContent, $revision->html);
        $filterConfig = HtmlContentFilterConfig::fromConfigString(config('app.content_filtering'));
        $filter = new HtmlContentFilter($filterConfig);

        return $filter->filterString($rawDiff);
    }

    /**
     * Destroy the given revision from the system.
     * Throws an exception if the user is attempting to delete the latest revision.
     * @throws RevisionDeleteException
     */
    public function destroyRevision(PageRevision $revision): void
    {
        $currentRevision = $revision->page->currentRevision;
        if (($currentRevision->id ?? 0) === $revision->id) {
            throw new RevisionDeleteException(trans('entities.revision_cannot_delete_latest'));
        }

        $revision->delete();
        Activity::add(ActivityType::REVISION_DELETE, $revision);
    }
}

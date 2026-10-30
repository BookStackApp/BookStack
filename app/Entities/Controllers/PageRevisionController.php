<?php

declare(strict_types=1);

namespace BookStack\Entities\Controllers;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Queries\PageRevisionQueries;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Entities\Repos\RevisionRepo;
use BookStack\Entities\Tools\PageContent;
use BookStack\Exceptions\NotFoundException;
use BookStack\Facades\Activity;
use BookStack\Http\Controller;
use BookStack\Permissions\Permission;
use BookStack\Util\HtmlContentFilter;
use BookStack\Util\HtmlContentFilterConfig;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Ssddanbrown\HtmlDiff\Diff;

class PageRevisionController extends Controller
{
    public function __construct(
        protected PageRepo $pageRepo,
        protected PageQueries $pageQueries,
        protected PageRevisionQueries $revisionQueries,
        protected RevisionRepo $revisionRepo,
    ) {
    }

    /**
     * Shows the last revisions for this page.
     *
     * @throws NotFoundException
     */
    public function index(Request $request, string $bookSlug, string $pageSlug): View
    {
        $this->checkPermission(Permission::RevisionViewAll);
        $page = $this->pageQueries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $listOptions = SimpleListOptions::fromRequest($request, 'page_revisions', true)->withSortOptions([
            'id' => trans('entities.pages_revisions_sort_number')
        ]);

        $revisions = $page->revisions()->select([
                'id', 'page_id', 'name', 'created_at', 'created_by', 'updated_at',
                'type', 'revision_number', 'summary',
            ])
            ->selectRaw("IF(markdown = '', false, true) as is_markdown")
            ->with(['page.book', 'createdBy'])
            ->reorder('id', $listOptions->getOrder())
            ->paginate(50);

        $this->setPageTitle(trans('entities.pages_revisions_named', ['pageName' => $page->getShortName()]));

        return view('pages.revisions', [
            'revisions'   => $revisions,
            'page'        => $page,
            'listOptions' => $listOptions,
            'oldestRevisionId' => $page->revisions()->min('id'),
        ]);
    }

    /**
     * Shows a preview of a single revision, via the page-local revision number.
     *
     * @throws NotFoundException
     */
    public function showByNumber(string $bookSlug, string $pageSlug, string $revisionNum): View
    {
        $this->checkPermission(Permission::RevisionViewAll);
        $page = $this->pageQueries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $revision = $this->revisionQueries->findVersionForPageByNumberOrFail($page->id, intval($revisionNum));

        return $this->show($page, $revision);
    }

    /**
     * Shows a preview of a single revision, looked up via the revision's ID.
     * Note: This is here for legacy support, with the above showByNumber intended to be used instead
     * as a more user-friendly identifier for the revision.
     */
    public function showById(string $bookSlug, string $pageSlug, string $revisionId): View
    {
        $this->checkPermission(Permission::RevisionViewAll);
        $page = $this->pageQueries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $revision = $this->revisionQueries->findVersionForPageByIdOrFail($page->id, intval($revisionId));

        return $this->show($page, $revision);
    }

    protected function show(Page $page, PageRevision $revision): View
    {
        $page->fill($revision->toArray());
        // TODO - Refactor PageContent so we don't need to juggle this
        $page->html = $revision->html;
        $page->html = (new PageContent($page))->render();

        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName' => $page->getShortName()]));

        return view('pages.revision', [
            'page'     => $page,
            'book'     => $page->book,
            'diff'     => null,
            'revision' => $revision,
        ]);
    }

    /**
     * Show the changes compared to the previous revision.
     */
    public function changesByNumber(string $bookSlug, string $pageSlug, string $revisionNum): View
    {
        $this->checkPermission(Permission::RevisionViewAll);
        $page = $this->pageQueries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $revision = $this->revisionQueries->findVersionForPageByNumberOrFail($page->id, intval($revisionNum));

        return $this->changes($page, $revision);
    }

    /**
     * Show the changes compared to the previous revision, with the lookup performed by revision ID.
     *
     * Note: This is here for legacy support, with the above showByNumber intended to be used instead
     * as a more user-friendly identifier for the revision.
     */
    public function changesById(string $bookSlug, string $pageSlug, string $revisionId): View
    {
        $this->checkPermission(Permission::RevisionViewAll);
        $page = $this->pageQueries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $revision = $this->revisionQueries->findVersionForPageByIdOrFail($page->id, intval($revisionId));

        return $this->changes($page, $revision);
    }

    protected function changes(Page $page, PageRevision $revision): View
    {
        $prev = $revision->getPreviousRevision();
        $prevContent = $prev->html ?? '';

        // TODO - Refactor PageContent so we can de-dupe these steps
        $rawDiff = Diff::excecute($prevContent, $revision->html);
        $filterConfig = HtmlContentFilterConfig::fromConfigString(config('app.content_filtering'));
        $filter = new HtmlContentFilter($filterConfig);
        $diff = $filter->filterString($rawDiff);

        $page->fill($revision->toArray());
        $page->html = '';
        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName' => $page->getShortName()]));

        return view('pages.revision', [
            'page'     => $page,
            'book'     => $page->book,
            'diff'     => $diff,
            'revision' => $revision,
        ]);
    }

    /**
     * Restores a page using the content of the specified revision.
     *
     * @throws NotFoundException
     */
    public function restore(string $bookSlug, string $pageSlug, int $revisionId): RedirectResponse
    {
        $this->checkPermission(Permission::RevisionViewAll);
        $page = $this->pageQueries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $this->checkOwnablePermission(Permission::PageUpdate, $page);

        $page = $this->pageRepo->restoreRevision($page, $revisionId);

        return redirect($page->getUrl());
    }

    /**
     * Deletes a revision using the id of the specified revision.
     *
     * @throws NotFoundException
     */
    public function destroy(string $bookSlug, string $pageSlug, string $revisionId): RedirectResponse
    {
        $this->checkPermission(Permission::RevisionViewAll);
        $page = $this->pageQueries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $this->checkOwnablePermission(Permission::PageDelete, $page);

        $revision = $this->revisionQueries->findVersionForPageByIdOrFail($page->id, intval($revisionId));

        // Check if it's the latest revision, cannot delete the latest revision.
        if (intval($page->currentRevision->id ?? null) === intval($revisionId)) {
            $this->showErrorNotification(trans('entities.revision_cannot_delete_latest'));

            return redirect($page->getUrl('/revisions'));
        }

        $revision->delete();
        Activity::add(ActivityType::REVISION_DELETE, $revision);

        return redirect($page->getUrl('/revisions'));
    }

    /**
     * Destroys existing drafts, belonging to the current user, for the given page.
     */
    public function destroyUserDraft(string $pageId): Response
    {
        $page = $this->pageQueries->findVisibleByIdOrFail(intval($pageId));
        $this->revisionRepo->deleteDraftsForCurrentUser($page);

        return response('', 200);
    }
}

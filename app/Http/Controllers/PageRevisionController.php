<?php namespace BookStack\Http\Controllers;

use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\NotFoundException;
use BookStack\Facades\Activity;
use GatherContent\Htmldiff\Htmldiff;

class PageRevisionController extends Controller
{

    protected $pageRepo;

    /**
     * PageRevisionController constructor.
     */
    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
        parent::__construct();
    }

    /**
     * Shows the last revisions for this page.
     * @throws NotFoundException
     */
    public function index(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->setPageTitle(trans('entities.pages_revisions_named', ['pageName'=>$page->getShortName()]));
        return view('pages.revisions', [
            'page' => $page,
            'current' => $page
        ]);
    }

    /**
     * Shows a preview of a single revision.
     * @throws NotFoundException
     */
    public function show(string $bookSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();
        if ($revision === null) {
            throw new NotFoundException();
        }

        $page->fill($revision->toArray());

        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName' => $page->getShortName()]));
        return view('pages.revision', [
            'page' => $page,
            'book' => $page->book,
            'diff' => null,
            'revision' => $revision
        ]);
    }

    /**
     * Shows the changes of a single revision.
     * @throws NotFoundException
     */
    public function changes(string $bookSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();
        if ($revision === null) {
            throw new NotFoundException();
        }

        $prev = $revision->getPrevious();
        $prevContent = $prev->html ?? '';
        $diff = (new Htmldiff)->diff($prevContent, $revision->html);

        $page->fill($revision->toArray());
        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName'=>$page->getShortName()]));

        return view('pages.revision', [
            'page' => $page,
            'book' => $page->book,
            'diff' => $diff,
            'revision' => $revision
        ]);
    }

    /**
     * Restores a page using the content of the specified revision.
     * @throws NotFoundException
     */
    public function restore(string $bookSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);

        $page = $this->pageRepo->restoreRevision($page, $revisionId);

        Activity::add($page, 'page_restore', $page->book->id);
        return redirect($page->getUrl());
    }

    /**
     * Deletes a revision using the id of the specified revision.
     * @throws NotFoundException
     */
    public function destroy(string $bookSlug, string $pageSlug, int $revId)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-delete', $page);

        $revision = $page->revisions()->where('id', '=', $revId)->first();
        if ($revision === null) {
            throw new NotFoundException("Revision #{$revId} not found");
        }

        // Get the current revision for the page
        $currentRevision = $page->getCurrentRevision();

        // Check if its the latest revision, cannot delete latest revision.
        if (intval($currentRevision->id) === intval($revId)) {
            $this->showErrorNotification(trans('entities.revision_cannot_delete_latest'));
            return redirect($page->getUrl('/revisions'));
        }

        $revision->delete();
        $this->showSuccessNotification(trans('entities.revision_delete_success'));
        return redirect($page->getUrl('/revisions'));
    }
}

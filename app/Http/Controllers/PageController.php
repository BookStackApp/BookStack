<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Entities\ExportService;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\NotFoundException;
use GatherContent\Htmldiff\Htmldiff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Views;

class PageController extends Controller
{

    protected $pageRepo;
    protected $exportService;
    protected $userRepo;

    /**
     * PageController constructor.
     * @param \BookStack\Entities\Repos\PageRepo $pageRepo
     * @param \BookStack\Entities\ExportService $exportService
     * @param UserRepo $userRepo
     */
    public function __construct(PageRepo $pageRepo, ExportService $exportService, UserRepo $userRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->exportService = $exportService;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Show the form for creating a new page.
     * @param string $bookSlug
     * @param string $chapterSlug
     * @return Response
     * @internal param bool $pageSlug
     * @throws NotFoundException
     */
    public function create($bookSlug, $chapterSlug = null)
    {
        if ($chapterSlug !== null) {
            $chapter = $this->pageRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
            $book = $chapter->book;
        } else {
            $chapter = null;
            $book = $this->pageRepo->getBySlug('book', $bookSlug);
        }

        $parent = $chapter ? $chapter : $book;
        $this->checkOwnablePermission('page-create', $parent);

        // Redirect to draft edit screen if signed in
        if ($this->signedIn) {
            $draft = $this->pageRepo->getDraftPage($book, $chapter);
            return redirect($draft->getUrl());
        }

        // Otherwise show the edit view if they're a guest
        $this->setPageTitle(trans('entities.pages_new'));
        return view('pages/guest-create', ['parent' => $parent]);
    }

    /**
     * Create a new page as a guest user.
     * @param Request $request
     * @param string $bookSlug
     * @param string|null $chapterSlug
     * @return mixed
     * @throws NotFoundException
     */
    public function createAsGuest(Request $request, $bookSlug, $chapterSlug = null)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        if ($chapterSlug !== null) {
            $chapter = $this->pageRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
            $book = $chapter->book;
        } else {
            $chapter = null;
            $book = $this->pageRepo->getBySlug('book', $bookSlug);
        }

        $parent = $chapter ? $chapter : $book;
        $this->checkOwnablePermission('page-create', $parent);

        $page = $this->pageRepo->getDraftPage($book, $chapter);
        $this->pageRepo->publishPageDraft($page, [
            'name' => $request->get('name'),
            'html' => ''
        ]);
        return redirect($page->getUrl('/edit'));
    }

    /**
     * Show form to continue editing a draft page.
     * @param string $bookSlug
     * @param int $pageId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editDraft($bookSlug, $pageId)
    {
        $draft = $this->pageRepo->getById('page', $pageId, true);
        $this->checkOwnablePermission('page-create', $draft->parent);
        $this->setPageTitle(trans('entities.pages_edit_draft'));

        $draftsEnabled = $this->signedIn;
        return view('pages/edit', [
            'page' => $draft,
            'book' => $draft->book,
            'isDraft' => true,
            'draftsEnabled' => $draftsEnabled
        ]);
    }

    /**
     * Store a new page by changing a draft into a page.
     * @param  Request $request
     * @param  string $bookSlug
     * @param  int $pageId
     * @return Response
     */
    public function store(Request $request, $bookSlug, $pageId)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        $input = $request->all();
        $draftPage = $this->pageRepo->getById('page', $pageId, true);
        $book = $draftPage->book;

        $parent = $draftPage->parent;
        $this->checkOwnablePermission('page-create', $parent);

        if ($parent->isA('chapter')) {
            $input['priority'] = $this->pageRepo->getNewChapterPriority($parent);
        } else {
            $input['priority'] = $this->pageRepo->getNewBookPriority($parent);
        }

        $page = $this->pageRepo->publishPageDraft($draftPage, $input);

        Activity::add($page, 'page_create', $book->id);
        return redirect($page->getUrl());
    }

    /**
     * Display the specified page.
     * If the page is not found via the slug the revisions are searched for a match.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return Response
     * @throws NotFoundException
     */
    public function show($bookSlug, $pageSlug)
    {
        try {
            $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        } catch (NotFoundException $e) {
            $page = $this->pageRepo->getPageByOldSlug($pageSlug, $bookSlug);
            if ($page === null) {
                throw $e;
            }
            return redirect($page->getUrl());
        }

        $this->checkOwnablePermission('page-view', $page);

        $page->html = $this->pageRepo->renderPage($page);
        $sidebarTree = $this->pageRepo->getBookChildren($page->book);
        $pageNav = $this->pageRepo->getPageNav($page->html);

        // check if the comment's are enabled
        $commentsEnabled = !setting('app-disable-comments');
        if ($commentsEnabled) {
            $page->load(['comments.createdBy']);
        }

        Views::add($page);
        $this->setPageTitle($page->getShortName());
        return view('pages/show', [
            'page' => $page,'book' => $page->book,
            'current' => $page,
            'sidebarTree' => $sidebarTree,
            'commentsEnabled' => $commentsEnabled,
            'pageNav' => $pageNav
        ]);
    }

    /**
     * Get page from an ajax request.
     * @param int $pageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPageAjax($pageId)
    {
        $page = $this->pageRepo->getById('page', $pageId);
        return response()->json($page);
    }

    /**
     * Show the form for editing the specified page.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return Response
     * @throws NotFoundException
     */
    public function edit($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->setPageTitle(trans('entities.pages_editing_named', ['pageName'=>$page->getShortName()]));
        $page->isDraft = false;

        // Check for active editing
        $warnings = [];
        if ($this->pageRepo->isPageEditingActive($page, 60)) {
            $warnings[] = $this->pageRepo->getPageEditingActiveMessage($page, 60);
        }

        // Check for a current draft version for this user
        $userPageDraft = $this->pageRepo->getUserPageDraft($page, $this->currentUser->id);
        if ($userPageDraft !== null) {
            $page->name = $userPageDraft->name;
            $page->html = $userPageDraft->html;
            $page->markdown = $userPageDraft->markdown;
            $page->isDraft = true;
            $warnings [] = $this->pageRepo->getUserPageDraftMessage($userPageDraft);
        }

        if (count($warnings) > 0) {
            session()->flash('warning', implode("\n", $warnings));
        }

        $draftsEnabled = $this->signedIn;
        return view('pages/edit', [
            'page' => $page,
            'book' => $page->book,
            'current' => $page,
            'draftsEnabled' => $draftsEnabled
        ]);
    }

    /**
     * Update the specified page in storage.
     * @param  Request $request
     * @param  string $bookSlug
     * @param  string $pageSlug
     * @return Response
     */
    public function update(Request $request, $bookSlug, $pageSlug)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->pageRepo->updatePage($page, $page->book->id, $request->all());
        Activity::add($page, 'page_update', $page->book->id);
        return redirect($page->getUrl());
    }

    /**
     * Save a draft update as a revision.
     * @param Request $request
     * @param int $pageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDraft(Request $request, $pageId)
    {
        $page = $this->pageRepo->getById('page', $pageId, true);
        $this->checkOwnablePermission('page-update', $page);

        if (!$this->signedIn) {
            return response()->json([
                'status' => 'error',
                'message' => trans('errors.guests_cannot_save_drafts'),
            ], 500);
        }

        $draft = $this->pageRepo->updatePageDraft($page, $request->only(['name', 'html', 'markdown']));

        $updateTime = $draft->updated_at->timestamp;
        return response()->json([
            'status'    => 'success',
            'message'   => trans('entities.pages_edit_draft_save_at'),
            'timestamp' => $updateTime
        ]);
    }

    /**
     * Redirect from a special link url which
     * uses the page id rather than the name.
     * @param int $pageId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectFromLink($pageId)
    {
        $page = $this->pageRepo->getById('page', $pageId);
        return redirect($page->getUrl());
    }

    /**
     * Show the deletion page for the specified page.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-delete', $page);
        $this->setPageTitle(trans('entities.pages_delete_named', ['pageName'=>$page->getShortName()]));
        return view('pages/delete', ['book' => $page->book, 'page' => $page, 'current' => $page]);
    }


    /**
     * Show the deletion page for the specified page.
     * @param string $bookSlug
     * @param int $pageId
     * @return \Illuminate\View\View
     * @throws NotFoundException
     */
    public function showDeleteDraft($bookSlug, $pageId)
    {
        $page = $this->pageRepo->getById('page', $pageId, true);
        $this->checkOwnablePermission('page-update', $page);
        $this->setPageTitle(trans('entities.pages_delete_draft_named', ['pageName'=>$page->getShortName()]));
        return view('pages/delete', ['book' => $page->book, 'page' => $page, 'current' => $page]);
    }

    /**
     * Remove the specified page from storage.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return Response
     * @internal param int $id
     */
    public function destroy($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $book = $page->book;
        $this->checkOwnablePermission('page-delete', $page);
        $this->pageRepo->destroyPage($page);

        Activity::addMessage('page_delete', $book->id, $page->name);
        session()->flash('success', trans('entities.pages_delete_success'));
        return redirect($book->getUrl());
    }

    /**
     * Remove the specified draft page from storage.
     * @param string $bookSlug
     * @param int $pageId
     * @return Response
     * @throws NotFoundException
     */
    public function destroyDraft($bookSlug, $pageId)
    {
        $page = $this->pageRepo->getById('page', $pageId, true);
        $book = $page->book;
        $this->checkOwnablePermission('page-update', $page);
        session()->flash('success', trans('entities.pages_delete_draft_success'));
        $this->pageRepo->destroyPage($page);
        return redirect($book->getUrl());
    }

    /**
     * Shows the last revisions for this page.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\View\View
     */
    public function showRevisions($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->setPageTitle(trans('entities.pages_revisions_named', ['pageName'=>$page->getShortName()]));
        return view('pages/revisions', ['page' => $page, 'book' => $page->book, 'current' => $page]);
    }

    /**
     * Shows a preview of a single revision
     * @param string $bookSlug
     * @param string $pageSlug
     * @param int $revisionId
     * @return \Illuminate\View\View
     */
    public function showRevision($bookSlug, $pageSlug, $revisionId)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();
        if ($revision === null) {
            abort(404);
        }

        $page->fill($revision->toArray());
        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName' => $page->getShortName()]));

        return view('pages/revision', [
            'page' => $page,
            'book' => $page->book,
            'revision' => $revision
        ]);
    }

    /**
     * Shows the changes of a single revision
     * @param string $bookSlug
     * @param string $pageSlug
     * @param int $revisionId
     * @return \Illuminate\View\View
     */
    public function showRevisionChanges($bookSlug, $pageSlug, $revisionId)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();
        if ($revision === null) {
            abort(404);
        }

        $prev = $revision->getPrevious();
        $prevContent = ($prev === null) ? '' : $prev->html;
        $diff = (new Htmldiff)->diff($prevContent, $revision->html);

        $page->fill($revision->toArray());
        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName'=>$page->getShortName()]));

        return view('pages/revision', [
            'page' => $page,
            'book' => $page->book,
            'diff' => $diff,
            'revision' => $revision
        ]);
    }

    /**
     * Restores a page using the content of the specified revision.
     * @param string $bookSlug
     * @param string $pageSlug
     * @param int $revisionId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function restoreRevision($bookSlug, $pageSlug, $revisionId)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        $page = $this->pageRepo->restorePageRevision($page, $page->book, $revisionId);
        Activity::add($page, 'page_restore', $page->book->id);
        return redirect($page->getUrl());
    }


    /**
     * Deletes a revision using the id of the specified revision.
     * @param string $bookSlug
     * @param string $pageSlug
     * @param int $revId
     * @throws NotFoundException
     * @throws BadRequestException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroyRevision($bookSlug, $pageSlug, $revId)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-delete', $page);

        $revision = $page->revisions()->where('id', '=', $revId)->first();
        if ($revision === null) {
            throw new NotFoundException("Revision #{$revId} not found");
        }

        // Get the current revision for the page
        $currentRevision = $page->getCurrentRevision();

        // Check if its the latest revision, cannot delete latest revision.
        if (intval($currentRevision->id) === intval($revId)) {
            session()->flash('error', trans('entities.revision_cannot_delete_latest'));
            return response()->view('pages/revisions', ['page' => $page, 'book' => $page->book, 'current' => $page], 400);
        }

        $revision->delete();
        session()->flash('success', trans('entities.revision_delete_success'));
        return view('pages/revisions', ['page' => $page, 'book' => $page->book, 'current' => $page]);
    }

    /**
     * Exports a page to a PDF.
     * https://github.com/barryvdh/laravel-dompdf
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $page->html = $this->pageRepo->renderPage($page);
        $pdfContent = $this->exportService->pageToPdf($page);
        return $this->downloadResponse($pdfContent, $pageSlug . '.pdf');
    }

    /**
     * Export a page to a self-contained HTML file.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\Http\Response
     */
    public function exportHtml($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $page->html = $this->pageRepo->renderPage($page);
        $containedHtml = $this->exportService->pageToContainedHtml($page);
        return $this->downloadResponse($containedHtml, $pageSlug . '.html');
    }

    /**
     * Export a page to a simple plaintext .txt file.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\Http\Response
     */
    public function exportPlainText($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $pageText = $this->exportService->pageToPlainText($page);
        return $this->downloadResponse($pageText, $pageSlug . '.txt');
    }

    /**
     * Show a listing of recently created pages
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRecentlyCreated()
    {
        $pages = $this->pageRepo->getRecentlyCreatedPaginated('page', 20)->setPath(baseUrl('/pages/recently-created'));
        return view('pages/detailed-listing', [
            'title' => trans('entities.recently_created_pages'),
            'pages' => $pages
        ]);
    }

    /**
     * Show a listing of recently created pages
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRecentlyUpdated()
    {
        $pages = $this->pageRepo->getRecentlyUpdatedPaginated('page', 20)->setPath(baseUrl('/pages/recently-updated'));
        return view('pages/detailed-listing', [
            'title' => trans('entities.recently_updated_pages'),
            'pages' => $pages
        ]);
    }

    /**
     * Show the Restrictions view.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRestrict($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $page);
        $roles = $this->userRepo->getRestrictableRoles();
        return view('pages/restrictions', [
            'page'  => $page,
            'roles' => $roles
        ]);
    }

    /**
     * Show the view to choose a new parent to move a page into.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return mixed
     * @throws NotFoundException
     */
    public function showMove($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('page-delete', $page);
        return view('pages/move', [
            'book' => $page->book,
            'page' => $page
        ]);
    }

    /**
     * Does the action of moving the location of a page
     * @param string $bookSlug
     * @param string $pageSlug
     * @param Request $request
     * @return mixed
     * @throws NotFoundException
     */
    public function move($bookSlug, $pageSlug, Request $request)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('page-delete', $page);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($page->getUrl());
        }

        $stringExploded = explode(':', $entitySelection);
        $entityType = $stringExploded[0];
        $entityId = intval($stringExploded[1]);


        try {
            $parent = $this->pageRepo->getById($entityType, $entityId);
        } catch (\Exception $e) {
            session()->flash(trans('entities.selected_book_chapter_not_found'));
            return redirect()->back();
        }

        $this->checkOwnablePermission('page-create', $parent);

        $this->pageRepo->changePageParent($page, $parent);
        Activity::add($page, 'page_move', $page->book->id);
        session()->flash('success', trans('entities.pages_move_success', ['parentName' => $parent->name]));

        return redirect($page->getUrl());
    }

    /**
     * Show the view to copy a page.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return mixed
     * @throws NotFoundException
     */
    public function showCopy($bookSlug, $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        session()->flashInput(['name' => $page->name]);
        return view('pages/copy', [
            'book' => $page->book,
            'page' => $page
        ]);
    }

    /**
     * Create a copy of a page within the requested target destination.
     * @param string $bookSlug
     * @param string $pageSlug
     * @param Request $request
     * @return mixed
     * @throws NotFoundException
     */
    public function copy($bookSlug, $pageSlug, Request $request)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            $parent = $page->chapter ? $page->chapter : $page->book;
        } else {
            $stringExploded = explode(':', $entitySelection);
            $entityType = $stringExploded[0];
            $entityId = intval($stringExploded[1]);

            try {
                $parent = $this->pageRepo->getById($entityType, $entityId);
            } catch (\Exception $e) {
                session()->flash(trans('entities.selected_book_chapter_not_found'));
                return redirect()->back();
            }
        }

        $this->checkOwnablePermission('page-create', $parent);

        $pageCopy = $this->pageRepo->copyPage($page, $parent, $request->get('name', ''));

        Activity::add($pageCopy, 'page_create', $pageCopy->book->id);
        session()->flash('success', trans('entities.pages_copy_success'));

        return redirect($pageCopy->getUrl());
    }

    /**
     * Set the permissions for this page.
     * @param string $bookSlug
     * @param string $pageSlug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws NotFoundException
     */
    public function restrict($bookSlug, $pageSlug, Request $request)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $page);
        $this->pageRepo->updateEntityPermissionsFromRequest($request, $page);
        session()->flash('success', trans('entities.pages_permissions_success'));
        return redirect($page->getUrl());
    }
}

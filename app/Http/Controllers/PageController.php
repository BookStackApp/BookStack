<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\BadRequestException;
use BookStack\Repos\EntityRepo;
use BookStack\Repos\UserRepo;
use BookStack\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Views;
use GatherContent\Htmldiff\Htmldiff;

class PageController extends Controller
{

    protected $entityRepo;
    protected $exportService;
    protected $userRepo;

    /**
     * PageController constructor.
     * @param EntityRepo $entityRepo
     * @param ExportService $exportService
     * @param UserRepo $userRepo
     */
    public function __construct(EntityRepo $entityRepo, ExportService $exportService, UserRepo $userRepo)
    {
        $this->entityRepo = $entityRepo;
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
            $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
            $book = $chapter->book;
        } else {
            $chapter = null;
            $book = $this->entityRepo->getBySlug('book', $bookSlug);
        }

        $parent = $chapter ? $chapter : $book;
        $this->checkOwnablePermission('page-create', $parent);

        // Redirect to draft edit screen if signed in
        if ($this->signedIn) {
            $draft = $this->entityRepo->getDraftPage($book, $chapter);
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
            $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
            $book = $chapter->book;
        } else {
            $chapter = null;
            $book = $this->entityRepo->getBySlug('book', $bookSlug);
        }

        $parent = $chapter ? $chapter : $book;
        $this->checkOwnablePermission('page-create', $parent);

        $page = $this->entityRepo->getDraftPage($book, $chapter);
        $this->entityRepo->publishPageDraft($page, [
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
        $draft = $this->entityRepo->getById('page', $pageId, true);
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
        $draftPage = $this->entityRepo->getById('page', $pageId, true);
        $book = $draftPage->book;

        $parent = $draftPage->parent;
        $this->checkOwnablePermission('page-create', $parent);

        if ($parent->isA('chapter')) {
            $input['priority'] = $this->entityRepo->getNewChapterPriority($parent);
        } else {
            $input['priority'] = $this->entityRepo->getNewBookPriority($parent);
        }

        $page = $this->entityRepo->publishPageDraft($draftPage, $input);

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
            $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        } catch (NotFoundException $e) {
            $page = $this->entityRepo->getPageByOldSlug($pageSlug, $bookSlug);
            if ($page === null) {
                throw $e;
            }
            return redirect($page->getUrl());
        }

        $this->checkOwnablePermission('page-view', $page);

        $page->html = $this->entityRepo->renderPage($page);
        $sidebarTree = $this->entityRepo->getBookChildren($page->book);
        $pageNav = $this->entityRepo->getPageNav($page->html);

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
        $page = $this->entityRepo->getById('page', $pageId);
        return response()->json($page);
    }

    /**
     * Show the form for editing the specified page.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return Response
     */
    public function edit($bookSlug, $pageSlug)
    {
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->setPageTitle(trans('entities.pages_editing_named', ['pageName'=>$page->getShortName()]));
        $page->isDraft = false;

        // Check for active editing
        $warnings = [];
        if ($this->entityRepo->isPageEditingActive($page, 60)) {
            $warnings[] = $this->entityRepo->getPageEditingActiveMessage($page, 60);
        }

        // Check for a current draft version for this user
        if ($this->entityRepo->hasUserGotPageDraft($page, $this->currentUser->id)) {
            $draft = $this->entityRepo->getUserPageDraft($page, $this->currentUser->id);
            $page->name = $draft->name;
            $page->html = $draft->html;
            $page->markdown = $draft->markdown;
            $page->isDraft = true;
            $warnings [] = $this->entityRepo->getUserPageDraftMessage($draft);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->entityRepo->updatePage($page, $page->book->id, $request->all());
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
        $page = $this->entityRepo->getById('page', $pageId, true);
        $this->checkOwnablePermission('page-update', $page);

        if (!$this->signedIn) {
            return response()->json([
                'status' => 'error',
                'message' => trans('errors.guests_cannot_save_drafts'),
            ], 500);
        }

        $draft = $this->entityRepo->updatePageDraft($page, $request->only(['name', 'html', 'markdown']));

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
        $page = $this->entityRepo->getById('page', $pageId);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
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
        $page = $this->entityRepo->getById('page', $pageId, true);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $book = $page->book;
        $this->checkOwnablePermission('page-delete', $page);
        $this->entityRepo->destroyPage($page);

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
        $page = $this->entityRepo->getById('page', $pageId, true);
        $book = $page->book;
        $this->checkOwnablePermission('page-update', $page);
        session()->flash('success', trans('entities.pages_delete_draft_success'));
        $this->entityRepo->destroyPage($page);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
        $page = $this->entityRepo->restorePageRevision($page, $page->book, $revisionId);
        Activity::add($page, 'page_restore', $page->book->id);
        return redirect($page->getUrl());
    }


    /**
     * Deletes a revision using the id of the specified revision.
     * @param string $bookSlug
     * @param string $pageSlug
     * @param int $revisionId
     * @throws NotFoundException
     * @throws BadRequestException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroyRevision($bookSlug, $pageSlug, $revId)
    {
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);

        $revision = $page->revisions()->where('id', '=', $revId)->first();
        if ($revision === null) {
            throw new NotFoundException("Revision #{$revId} not found");
        }

        // Get the current revision for the page
        $current = $revision->getCurrent();

        // Check if its the latest revision, cannot delete latest revision.
        if (intval($current->id) === intval($revId)) {
            throw new BadRequestException("Cannot delete the current revision #{$revId}");
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $page->html = $this->entityRepo->renderPage($page);
        $pdfContent = $this->exportService->pageToPdf($page);
        return response()->make($pdfContent, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $pageSlug . '.pdf'
        ]);
    }

    /**
     * Export a page to a self-contained HTML file.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\Http\Response
     */
    public function exportHtml($bookSlug, $pageSlug)
    {
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $page->html = $this->entityRepo->renderPage($page);
        $containedHtml = $this->exportService->pageToContainedHtml($page);
        return response()->make($containedHtml, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $pageSlug . '.html'
        ]);
    }

    /**
     * Export a page to a simple plaintext .txt file.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\Http\Response
     */
    public function exportPlainText($bookSlug, $pageSlug)
    {
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $containedHtml = $this->exportService->pageToPlainText($page);
        return response()->make($containedHtml, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $pageSlug . '.txt'
        ]);
    }

    /**
     * Show a listing of recently created pages
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRecentlyCreated()
    {
        $pages = $this->entityRepo->getRecentlyCreatedPaginated('page', 20)->setPath(baseUrl('/pages/recently-created'));
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
        $pages = $this->entityRepo->getRecentlyUpdatedPaginated('page', 20)->setPath(baseUrl('/pages/recently-updated'));
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($page->getUrl());
        }

        $stringExploded = explode(':', $entitySelection);
        $entityType = $stringExploded[0];
        $entityId = intval($stringExploded[1]);


        try {
            $parent = $this->entityRepo->getById($entityType, $entityId);
        } catch (\Exception $e) {
            session()->flash(trans('entities.selected_book_chapter_not_found'));
            return redirect()->back();
        }

        $this->checkOwnablePermission('page-create', $parent);

        $this->entityRepo->changePageParent($page, $parent);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $page);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            $parent = $page->chapter ? $page->chapter : $page->book;
        } else {
            $stringExploded = explode(':', $entitySelection);
            $entityType = $stringExploded[0];
            $entityId = intval($stringExploded[1]);

            try {
                $parent = $this->entityRepo->getById($entityType, $entityId);
            } catch (\Exception $e) {
                session()->flash(trans('entities.selected_book_chapter_not_found'));
                return redirect()->back();
            }
        }

        $this->checkOwnablePermission('page-create', $parent);

        $pageCopy = $this->entityRepo->copyPage($page, $parent, $request->get('name', ''));

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
        $page = $this->entityRepo->getBySlug('page', $pageSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $page);
        $this->entityRepo->updateEntityPermissionsFromRequest($request, $page);
        session()->flash('success', trans('entities.pages_permissions_success'));
        return redirect($page->getUrl());
    }
}

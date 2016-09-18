<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Exceptions\NotFoundException;
use BookStack\Repos\UserRepo;
use BookStack\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use BookStack\Http\Requests;
use BookStack\Repos\BookRepo;
use BookStack\Repos\ChapterRepo;
use BookStack\Repos\PageRepo;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Views;

class PageController extends Controller
{

    protected $pageRepo;
    protected $bookRepo;
    protected $chapterRepo;
    protected $exportService;
    protected $userRepo;

    /**
     * PageController constructor.
     * @param PageRepo $pageRepo
     * @param BookRepo $bookRepo
     * @param ChapterRepo $chapterRepo
     * @param ExportService $exportService
     * @param UserRepo $userRepo
     */
    public function __construct(PageRepo $pageRepo, BookRepo $bookRepo, ChapterRepo $chapterRepo, ExportService $exportService, UserRepo $userRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->bookRepo = $bookRepo;
        $this->chapterRepo = $chapterRepo;
        $this->exportService = $exportService;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Show the form for creating a new page.
     * @param string $bookSlug
     * @param bool $chapterSlug
     * @return Response
     * @internal param bool $pageSlug
     */
    public function create($bookSlug, $chapterSlug = false)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $chapterSlug ? $this->chapterRepo->getBySlug($chapterSlug, $book->id) : null;
        $parent = $chapter ? $chapter : $book;
        $this->checkOwnablePermission('page-create', $parent);
        $this->setPageTitle('Create New Page');

        $draft = $this->pageRepo->getDraftPage($book, $chapter);
        return redirect($draft->getUrl());
    }

    /**
     * Show form to continue editing a draft page.
     * @param string $bookSlug
     * @param int $pageId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editDraft($bookSlug, $pageId)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $draft = $this->pageRepo->getById($pageId, true);
        $this->checkOwnablePermission('page-create', $book);
        $this->setPageTitle('Edit Page Draft');

        return view('pages/edit', ['page' => $draft, 'book' => $book, 'isDraft' => true]);
    }

    /**
     * Store a new page by changing a draft into a page.
     * @param  Request $request
     * @param  string $bookSlug
     * @return Response
     */
    public function store(Request $request, $bookSlug, $pageId)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        $input = $request->all();
        $book = $this->bookRepo->getBySlug($bookSlug);

        $draftPage = $this->pageRepo->getById($pageId, true);

        $chapterId = intval($draftPage->chapter_id);
        $parent = $chapterId !== 0 ? $this->chapterRepo->getById($chapterId) : $book;
        $this->checkOwnablePermission('page-create', $parent);

        if ($parent->isA('chapter')) {
            $input['priority'] = $this->chapterRepo->getNewPriority($parent);
        } else {
            $input['priority'] = $this->bookRepo->getNewPriority($parent);
        }

        $page = $this->pageRepo->publishDraft($draftPage, $input);

        Activity::add($page, 'page_create', $book->id);
        return redirect($page->getUrl());
    }

    /**
     * Display the specified page.
     * If the page is not found via the slug the
     * revisions are searched for a match.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return Response
     */
    public function show($bookSlug, $pageSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);

        try {
            $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        } catch (NotFoundException $e) {
            $page = $this->pageRepo->findPageUsingOldSlug($pageSlug, $bookSlug);
            if ($page === null) abort(404);
            return redirect($page->getUrl());
        }

        $this->checkOwnablePermission('page-view', $page);

        $sidebarTree = $this->bookRepo->getChildren($book);
        $pageNav = $this->pageRepo->getPageNav($page);
        
        Views::add($page);
        $this->setPageTitle($page->getShortName());
        return view('pages/show', ['page' => $page, 'book' => $book,
                                   'current' => $page, 'sidebarTree' => $sidebarTree, 'pageNav' => $pageNav]);
    }

    /**
     * Get page from an ajax request.
     * @param int $pageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPageAjax($pageId)
    {
        $page = $this->pageRepo->getById($pageId);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->checkOwnablePermission('page-update', $page);
        $this->setPageTitle('Editing Page ' . $page->getShortName());
        $page->isDraft = false;

        // Check for active editing
        $warnings = [];
        if ($this->pageRepo->isPageEditingActive($page, 60)) {
            $warnings[] = $this->pageRepo->getPageEditingActiveMessage($page, 60);
        }

        // Check for a current draft version for this user
        if ($this->pageRepo->hasUserGotPageDraft($page, $this->currentUser->id)) {
            $draft = $this->pageRepo->getUserPageDraft($page, $this->currentUser->id);
            $page->name = $draft->name;
            $page->html = $draft->html;
            $page->markdown = $draft->markdown;
            $page->isDraft = true;
            $warnings [] = $this->pageRepo->getUserPageDraftMessage($draft);
        }

        if (count($warnings) > 0) session()->flash('warning', implode("\n", $warnings));

        return view('pages/edit', ['page' => $page, 'book' => $book, 'current' => $page]);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->checkOwnablePermission('page-update', $page);
        $this->pageRepo->updatePage($page, $book->id, $request->all());
        Activity::add($page, 'page_update', $book->id);
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
        $page = $this->pageRepo->getById($pageId, true);
        $this->checkOwnablePermission('page-update', $page);
        if ($page->draft) {
            $draft = $this->pageRepo->updateDraftPage($page, $request->only(['name', 'html', 'markdown']));
        } else {
            $draft = $this->pageRepo->saveUpdateDraft($page, $request->only(['name', 'html', 'markdown']));
        }

        $updateTime = $draft->updated_at->timestamp;
        $utcUpdateTimestamp = $updateTime + Carbon::createFromTimestamp(0)->offset;
        return response()->json([
            'status'    => 'success',
            'message'   => 'Draft saved at ',
            'timestamp' => $utcUpdateTimestamp
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
        $page = $this->pageRepo->getById($pageId);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->checkOwnablePermission('page-delete', $page);
        $this->setPageTitle('Delete Page ' . $page->getShortName());
        return view('pages/delete', ['book' => $book, 'page' => $page, 'current' => $page]);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getById($pageId, true);
        $this->checkOwnablePermission('page-update', $page);
        $this->setPageTitle('Delete Draft Page ' . $page->getShortName());
        return view('pages/delete', ['book' => $book, 'page' => $page, 'current' => $page]);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->checkOwnablePermission('page-delete', $page);
        Activity::addMessage('page_delete', $book->id, $page->name);
        session()->flash('success', 'Page deleted');
        $this->pageRepo->destroy($page);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getById($pageId, true);
        $this->checkOwnablePermission('page-update', $page);
        session()->flash('success', 'Draft deleted');
        $this->pageRepo->destroy($page);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->setPageTitle('Revisions For ' . $page->getShortName());
        return view('pages/revisions', ['page' => $page, 'book' => $book, 'current' => $page]);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $revision = $this->pageRepo->getRevisionById($revisionId);
        $page->fill($revision->toArray());
        $this->setPageTitle('Page Revision For ' . $page->getShortName());
        return view('pages/revision', ['page' => $page, 'book' => $book]);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->checkOwnablePermission('page-update', $page);
        $page = $this->pageRepo->restoreRevision($page, $book, $revisionId);
        Activity::add($page, 'page_restore', $book->id);
        return redirect($page->getUrl());
    }

    /**
     * Exports a page to pdf format using barryvdh/laravel-dompdf wrapper.
     * https://github.com/barryvdh/laravel-dompdf
     * @param string $bookSlug
     * @param string $pageSlug
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($bookSlug, $pageSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
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
        $pages = $this->pageRepo->getRecentlyCreatedPaginated(20)->setPath(baseUrl('/pages/recently-created'));
        return view('pages/detailed-listing', [
            'title' => 'Recently Created Pages',
            'pages' => $pages
        ]);
    }

    /**
     * Show a listing of recently created pages
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRecentlyUpdated()
    {
        $pages = $this->pageRepo->getRecentlyUpdatedPaginated(20)->setPath(baseUrl('/pages/recently-updated'));
        return view('pages/detailed-listing', [
            'title' => 'Recently Updated Pages',
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->checkOwnablePermission('page-update', $page);
        return view('pages/move', [
            'book' => $book,
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->checkOwnablePermission('page-update', $page);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($page->getUrl());
        }

        $stringExploded = explode(':', $entitySelection);
        $entityType = $stringExploded[0];
        $entityId = intval($stringExploded[1]);

        $parent = false;

        if ($entityType == 'chapter') {
            $parent = $this->chapterRepo->getById($entityId);
        } else if ($entityType == 'book') {
            $parent = $this->bookRepo->getById($entityId);
        }

        if ($parent === false || $parent === null) {
            session()->flash('The selected Book or Chapter was not found');
            return redirect()->back();
        }

        $this->pageRepo->changePageParent($page, $parent);
        Activity::add($page, 'page_move', $page->book->id);
        session()->flash('success', sprintf('Page moved to "%s"', $parent->name));

        return redirect($page->getUrl());
    }

    /**
     * Set the permissions for this page.
     * @param string $bookSlug
     * @param string $pageSlug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function restrict($bookSlug, $pageSlug, Request $request)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->checkOwnablePermission('restrictions-manage', $page);
        $this->pageRepo->updateEntityPermissionsFromRequest($request, $page);
        session()->flash('success', 'Page Permissions Updated');
        return redirect($page->getUrl());
    }

}

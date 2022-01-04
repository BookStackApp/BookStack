<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\View;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\Cloner;
use BookStack\Entities\Tools\NextPreviousContentLocator;
use BookStack\Entities\Tools\PageContent;
use BookStack\Entities\Tools\PageEditActivity;
use BookStack\Entities\Tools\PermissionsUpdater;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\PermissionsException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class PageController extends Controller
{
    protected $pageRepo;

    /**
     * PageController constructor.
     */
    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * Show the form for creating a new page.
     *
     * @throws Throwable
     */
    public function create(string $bookSlug, string $chapterSlug = null)
    {
        $parent = $this->pageRepo->getParentFromSlugs($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('page-create', $parent);

        // Redirect to draft edit screen if signed in
        if ($this->isSignedIn()) {
            $draft = $this->pageRepo->getNewDraftPage($parent);

            return redirect($draft->getUrl());
        }

        // Otherwise show the edit view if they're a guest
        $this->setPageTitle(trans('entities.pages_new'));

        return view('pages.guest-create', ['parent' => $parent]);
    }

    /**
     * Create a new page as a guest user.
     *
     * @throws ValidationException
     */
    public function createAsGuest(Request $request, string $bookSlug, string $chapterSlug = null)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $parent = $this->pageRepo->getParentFromSlugs($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('page-create', $parent);

        $page = $this->pageRepo->getNewDraftPage($parent);
        $this->pageRepo->publishDraft($page, [
            'name' => $request->get('name'),
            'html' => '',
        ]);

        return redirect($page->getUrl('/edit'));
    }

    /**
     * Show form to continue editing a draft page.
     *
     * @throws NotFoundException
     */
    public function editDraft(string $bookSlug, int $pageId)
    {
        $draft = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-create', $draft->getParent());
        $this->setPageTitle(trans('entities.pages_edit_draft'));

        $draftsEnabled = $this->isSignedIn();
        $templates = $this->pageRepo->getTemplates(10);

        return view('pages.edit', [
            'page'          => $draft,
            'book'          => $draft->book,
            'isDraft'       => true,
            'draftsEnabled' => $draftsEnabled,
            'templates'     => $templates,
        ]);
    }

    /**
     * Store a new page by changing a draft into a page.
     *
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function store(Request $request, string $bookSlug, int $pageId)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);
        $draftPage = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-create', $draftPage->getParent());

        $page = $this->pageRepo->publishDraft($draftPage, $request->all());

        return redirect($page->getUrl());
    }

    /**
     * Display the specified page.
     * If the page is not found via the slug the revisions are searched for a match.
     *
     * @throws NotFoundException
     */
    public function show(string $bookSlug, string $pageSlug)
    {
        try {
            $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        } catch (NotFoundException $e) {
            $page = $this->pageRepo->getByOldSlug($bookSlug, $pageSlug);

            if ($page === null) {
                throw $e;
            }

            return redirect($page->getUrl());
        }

        $this->checkOwnablePermission('page-view', $page);

        $pageContent = (new PageContent($page));
        $page->html = $pageContent->render();
        $sidebarTree = (new BookContents($page->book))->getTree();
        $pageNav = $pageContent->getNavigation($page->html);

        // Check if page comments are enabled
        $commentsEnabled = !setting('app-disable-comments');
        if ($commentsEnabled) {
            $page->load(['comments.createdBy']);
        }

        $nextPreviousLocator = new NextPreviousContentLocator($page, $sidebarTree);

        View::incrementFor($page);
        $this->setPageTitle($page->getShortName());

        return view('pages.show', [
            'page'            => $page,
            'book'            => $page->book,
            'current'         => $page,
            'sidebarTree'     => $sidebarTree,
            'commentsEnabled' => $commentsEnabled,
            'pageNav'         => $pageNav,
            'next'            => $nextPreviousLocator->getNext(),
            'previous'        => $nextPreviousLocator->getPrevious(),
        ]);
    }

    /**
     * Get page from an ajax request.
     *
     * @throws NotFoundException
     */
    public function getPageAjax(int $pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        $page->setHidden(array_diff($page->getHidden(), ['html', 'markdown']));
        $page->makeHidden(['book']);

        return response()->json($page);
    }

    /**
     * Show the form for editing the specified page.
     *
     * @throws NotFoundException
     */
    public function edit(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);

        $page->isDraft = false;
        $editActivity = new PageEditActivity($page);

        // Check for active editing
        $warnings = [];
        if ($editActivity->hasActiveEditing()) {
            $warnings[] = $editActivity->activeEditingMessage();
        }

        // Check for a current draft version for this user
        $userDraft = $this->pageRepo->getUserDraft($page);
        if ($userDraft !== null) {
            $page->forceFill($userDraft->only(['name', 'html', 'markdown']));
            $page->isDraft = true;
            $warnings[] = $editActivity->getEditingActiveDraftMessage($userDraft);
        }

        if (count($warnings) > 0) {
            $this->showWarningNotification(implode("\n", $warnings));
        }

        $templates = $this->pageRepo->getTemplates(10);
        $draftsEnabled = $this->isSignedIn();
        $this->setPageTitle(trans('entities.pages_editing_named', ['pageName' => $page->getShortName()]));

        return view('pages.edit', [
            'page'          => $page,
            'book'          => $page->book,
            'current'       => $page,
            'draftsEnabled' => $draftsEnabled,
            'templates'     => $templates,
        ]);
    }

    /**
     * Update the specified page in storage.
     *
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function update(Request $request, string $bookSlug, string $pageSlug)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);

        $this->pageRepo->update($page, $request->all());

        return redirect($page->getUrl());
    }

    /**
     * Save a draft update as a revision.
     *
     * @throws NotFoundException
     */
    public function saveDraft(Request $request, int $pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-update', $page);

        if (!$this->isSignedIn()) {
            return $this->jsonError(trans('errors.guests_cannot_save_drafts'), 500);
        }

        $draft = $this->pageRepo->updatePageDraft($page, $request->only(['name', 'html', 'markdown']));
        $warnings = (new PageEditActivity($page))->getWarningMessagesForDraft($draft);

        return response()->json([
            'status'    => 'success',
            'message'   => trans('entities.pages_edit_draft_save_at'),
            'warning'   => implode("\n", $warnings),
            'timestamp' => $draft->updated_at->timestamp,
        ]);
    }

    /**
     * Redirect from a special link url which uses the page id rather than the name.
     *
     * @throws NotFoundException
     */
    public function redirectFromLink(int $pageId)
    {
        $page = $this->pageRepo->getById($pageId);

        return redirect($page->getUrl());
    }

    /**
     * Show the deletion page for the specified page.
     *
     * @throws NotFoundException
     */
    public function showDelete(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-delete', $page);
        $this->setPageTitle(trans('entities.pages_delete_named', ['pageName' => $page->getShortName()]));

        return view('pages.delete', [
            'book'    => $page->book,
            'page'    => $page,
            'current' => $page,
        ]);
    }

    /**
     * Show the deletion page for the specified page.
     *
     * @throws NotFoundException
     */
    public function showDeleteDraft(string $bookSlug, int $pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-update', $page);
        $this->setPageTitle(trans('entities.pages_delete_draft_named', ['pageName' => $page->getShortName()]));

        return view('pages.delete', [
            'book'    => $page->book,
            'page'    => $page,
            'current' => $page,
        ]);
    }

    /**
     * Remove the specified page from storage.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function destroy(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-delete', $page);
        $parent = $page->getParent();

        $this->pageRepo->destroy($page);

        return redirect($parent->getUrl());
    }

    /**
     * Remove the specified draft page from storage.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function destroyDraft(string $bookSlug, int $pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        $book = $page->book;
        $chapter = $page->chapter;
        $this->checkOwnablePermission('page-update', $page);

        $this->pageRepo->destroy($page);

        $this->showSuccessNotification(trans('entities.pages_delete_draft_success'));

        if ($chapter && userCan('view', $chapter)) {
            return redirect($chapter->getUrl());
        }

        return redirect($book->getUrl());
    }

    /**
     * Show a listing of recently created pages.
     */
    public function showRecentlyUpdated()
    {
        $pages = Page::visible()->orderBy('updated_at', 'desc')
            ->paginate(20)
            ->setPath(url('/pages/recently-updated'));

        $this->setPageTitle(trans('entities.recently_updated_pages'));

        return view('common.detailed-listing-paginated', [
            'title'    => trans('entities.recently_updated_pages'),
            'entities' => $pages,
        ]);
    }

    /**
     * Show the view to choose a new parent to move a page into.
     *
     * @throws NotFoundException
     */
    public function showMove(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('page-delete', $page);

        return view('pages.move', [
            'book' => $page->book,
            'page' => $page,
        ]);
    }

    /**
     * Does the action of moving the location of a page.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function move(Request $request, string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('page-delete', $page);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($page->getUrl());
        }

        try {
            $parent = $this->pageRepo->move($page, $entitySelection);
        } catch (Exception $exception) {
            if ($exception instanceof PermissionsException) {
                $this->showPermissionError();
            }

            $this->showErrorNotification(trans('errors.selected_book_chapter_not_found'));

            return redirect()->back();
        }

        $this->showSuccessNotification(trans('entities.pages_move_success', ['parentName' => $parent->name]));

        return redirect($page->getUrl());
    }

    /**
     * Show the view to copy a page.
     *
     * @throws NotFoundException
     */
    public function showCopy(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-view', $page);
        session()->flashInput(['name' => $page->name]);

        return view('pages.copy', [
            'book' => $page->book,
            'page' => $page,
        ]);
    }

    /**
     * Create a copy of a page within the requested target destination.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function copy(Request $request, Cloner $cloner, string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-view', $page);

        $entitySelection = $request->get('entity_selection') ?: null;
        $newParent = $entitySelection ? $this->pageRepo->findParentByIdentifier($entitySelection) : $page->getParent();

        if (is_null($newParent)) {
            $this->showErrorNotification(trans('errors.selected_book_chapter_not_found'));

            return redirect()->back();
        }

        $this->checkOwnablePermission('page-create', $newParent);

        $newName = $request->get('name') ?: $page->name;
        $pageCopy = $cloner->clonePage($page, $newParent, $newName);
        $this->showSuccessNotification(trans('entities.pages_copy_success'));

        return redirect($pageCopy->getUrl());
    }

    /**
     * Show the Permissions view.
     *
     * @throws NotFoundException
     */
    public function showPermissions(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('restrictions-manage', $page);

        return view('pages.permissions', [
            'page' => $page,
        ]);
    }

    /**
     * Set the permissions for this page.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function permissions(Request $request, PermissionsUpdater $permissionsUpdater, string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('restrictions-manage', $page);

        $permissionsUpdater->updateFromPermissionsForm($page, $request);

        $this->showSuccessNotification(trans('entities.pages_permissions_success'));

        return redirect($page->getUrl());
    }
}

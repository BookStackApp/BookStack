<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Managers\BookContents;
use BookStack\Entities\Managers\PageContent;
use BookStack\Entities\Managers\PageEditActivity;
use BookStack\Entities\Page;
use BookStack\Entities\Repos\NewPageRepo;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\NotifyException;
use BookStack\Exceptions\PermissionsException;
use Exception;
use GatherContent\Htmldiff\Htmldiff;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;
use Views;

class PageController extends Controller
{

    protected $newPageRepo;
    protected $pageRepo;
    protected $userRepo;

    /**
     * PageController constructor.
     */
    public function __construct(NewPageRepo $newPageRepo, PageRepo $pageRepo, UserRepo $userRepo)
    {
        $this->newPageRepo = $newPageRepo;
        $this->pageRepo = $pageRepo;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Show the form for creating a new page.
     * @throws Throwable
     */
    public function create(string $bookSlug, string $chapterSlug = null)
    {
        $parent = $this->newPageRepo->getParentFromSlugs($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('page-create', $parent);

        // Redirect to draft edit screen if signed in
        if ($this->isSignedIn()) {
            $draft = $this->newPageRepo->getNewDraftPage($parent);
            return redirect($draft->getUrl());
        }

        // Otherwise show the edit view if they're a guest
        $this->setPageTitle(trans('entities.pages_new'));
        return view('pages.guest-create', ['parent' => $parent]);
    }

    /**
     * Create a new page as a guest user.
     * @throws ValidationException
     */
    public function createAsGuest(Request $request, string $bookSlug, string $chapterSlug = null)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        $parent = $this->newPageRepo->getParentFromSlugs($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('page-create', $parent);

        $page = $this->newPageRepo->getNewDraftPage($parent);
        $this->newPageRepo->publishDraft($page, [
            'name' => $request->get('name'),
            'html' => ''
        ]);

        return redirect($page->getUrl('/edit'));
    }

    /**
     * Show form to continue editing a draft page.
     * @throws NotFoundException
     */
    public function editDraft(string $bookSlug, int $pageId)
    {
        $draft = $this->newPageRepo->getById($pageId);
        $this->checkOwnablePermission('page-create', $draft->parent);
        $this->setPageTitle(trans('entities.pages_edit_draft'));

        $draftsEnabled = $this->isSignedIn();
        $templates = $this->newPageRepo->getTemplates(10);

        return view('pages.edit', [
            'page' => $draft,
            'book' => $draft->book,
            'isDraft' => true,
            'draftsEnabled' => $draftsEnabled,
            'templates' => $templates,
        ]);
    }

    /**
     * Store a new page by changing a draft into a page.
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function store(Request $request, string $bookSlug, int $pageId)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);
        $draftPage = $this->newPageRepo->getById($pageId);
        $this->checkOwnablePermission('page-create', $draftPage->parent);

        $page = $this->newPageRepo->publishDraft($draftPage, $request->all());
        Activity::add($page, 'page_create', $draftPage->book->id);

        return redirect($page->getUrl());
    }

    /**
     * Display the specified page.
     * If the page is not found via the slug the revisions are searched for a match.
     * @throws NotFoundException
     */
    public function show(string $bookSlug, string $pageSlug)
    {
        try {
            $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        } catch (NotFoundException $e) {
            $page = $this->newPageRepo->getByOldSlug($bookSlug, $pageSlug);

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

        Views::add($page);
        $this->setPageTitle($page->getShortName());
        return view('pages.show', [
            'page' => $page,
            'book' => $page->book,
            'current' => $page,
            'sidebarTree' => $sidebarTree,
            'commentsEnabled' => $commentsEnabled,
            'pageNav' => $pageNav
        ]);
    }

    /**
     * Get page from an ajax request.
     * @throws NotFoundException
     */
    public function getPageAjax(int $pageId)
    {
        $page = $this->newPageRepo->getById($pageId);
        return response()->json($page);
    }

    /**
     * Show the form for editing the specified page.
     * @throws NotFoundException
     */
    public function edit(string $bookSlug, string $pageSlug)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);

        $page->isDraft = false;
        $editActivity = new PageEditActivity($page);

        // Check for active editing
        $warnings = [];
        if ($editActivity->hasActiveEditing()) {
            $warnings[] = $editActivity->activeEditingMessage();
        }

        // Check for a current draft version for this user
        $userDraft = $this->newPageRepo->getUserDraft($page);
        if ($userDraft !== null) {
            $page->forceFill($userDraft->only(['name', 'html', 'markdown']));
            $page->isDraft = true;
            $warnings[] = $editActivity->getEditingActiveDraftMessage($userDraft);
        }

        if (count($warnings) > 0) {
            $this->showWarningNotification( implode("\n", $warnings));
        }

        $templates = $this->pageRepo->getPageTemplates(10);
        $draftsEnabled = $this->isSignedIn();
        $this->setPageTitle(trans('entities.pages_editing_named', ['pageName' => $page->getShortName()]));
        return view('pages.edit', [
            'page' => $page,
            'book' => $page->book,
            'current' => $page,
            'draftsEnabled' => $draftsEnabled,
            'templates' => $templates,
        ]);
    }

    /**
     * Update the specified page in storage.
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function update(Request $request, string $bookSlug, string $pageSlug)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);

        $this->newPageRepo->update($page, $request->all());
        Activity::add($page, 'page_update', $page->book->id);

        return redirect($page->getUrl());
    }

    /**
     * Save a draft update as a revision.
     * @throws NotFoundException
     */
    public function saveDraft(Request $request, int $pageId)
    {
        $page = $this->newPageRepo->getById($pageId);
        $this->checkOwnablePermission('page-update', $page);

        if (!$this->isSignedIn()) {
            return $this->jsonError(trans('errors.guests_cannot_save_drafts'), 500);
        }

        $draft = $this->newPageRepo->updatePageDraft($page, $request->only(['name', 'html', 'markdown']));

        $updateTime = $draft->updated_at->timestamp;
        return response()->json([
            'status'    => 'success',
            'message'   => trans('entities.pages_edit_draft_save_at'),
            'timestamp' => $updateTime
        ]);
    }

    /**
     * Redirect from a special link url which uses the page id rather than the name.
     * @throws NotFoundException
     */
    public function redirectFromLink(int $pageId)
    {
        $page = $this->newPageRepo->getById($pageId);
        return redirect($page->getUrl());
    }

    /**
     * Show the deletion page for the specified page.
     * @throws NotFoundException
     */
    public function showDelete(string $bookSlug, string $pageSlug)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-delete', $page);
        $this->setPageTitle(trans('entities.pages_delete_named', ['pageName'=>$page->getShortName()]));
        return view('pages.delete', [
            'book' => $page->book,
            'page' => $page,
            'current' => $page
        ]);
    }

    /**
     * Show the deletion page for the specified page.
     * @throws NotFoundException
     */
    public function showDeleteDraft(string $bookSlug, int $pageId)
    {
        $page = $this->newPageRepo->getById($pageId);
        $this->checkOwnablePermission('page-update', $page);
        $this->setPageTitle(trans('entities.pages_delete_draft_named', ['pageName'=>$page->getShortName()]));
        return view('pages.delete', [
            'book' => $page->book,
            'page' => $page,
            'current' => $page
        ]);
    }

    /**
     * Remove the specified page from storage.
     * @throws NotFoundException
     * @throws Throwable
     * @throws NotifyException
     */
    public function destroy(string $bookSlug, string $pageSlug)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-delete', $page);

        $book = $page->book;
        $this->newPageRepo->destroy($page);
        Activity::addMessage('page_delete', $page->name, $book->id);

        $this->showSuccessNotification( trans('entities.pages_delete_success'));
        return redirect($book->getUrl());
    }

    /**
     * Remove the specified draft page from storage.
     * @throws NotFoundException
     * @throws NotifyException
     * @throws Throwable
     */
    public function destroyDraft(string $bookSlug, int $pageId)
    {
        $page = $this->newPageRepo->getById($pageId);
        $book = $page->book;
        $this->checkOwnablePermission('page-update', $page);

        $this->newPageRepo->destroy($page);

        $this->showSuccessNotification( trans('entities.pages_delete_draft_success'));
        return redirect($book->getUrl());
    }

    /**
     * Shows the last revisions for this page.
     * @throws NotFoundException
     */
    public function showRevisions(string $bookSlug, string $pageSlug)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
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
    public function showRevision(string $bookSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
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
    public function showRevisionChanges(string $bookSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
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
    public function restoreRevision(string $bookSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);

        $page = $this->newPageRepo->restoreRevision($page, $revisionId);

        Activity::add($page, 'page_restore', $page->book->id);
        return redirect($page->getUrl());
    }

    /**
     * Deletes a revision using the id of the specified revision.
     * @throws NotFoundException
     */
    public function destroyRevision(string $bookSlug, string $pageSlug, int $revId)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
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
            return $this->showRevisions($bookSlug, $pageSlug);
        }

        $revision->delete();
        $this->showSuccessNotification( trans('entities.revision_delete_success'));
        return redirect($page->getUrl('/revisions'));
    }

    /**
     * Show a listing of recently created pages.
     */
    public function showRecentlyUpdated()
    {
        $pages = Page::visible()->orderBy('updated_at', 'desc')
            ->paginate(20)
            ->setPath(url('/pages/recently-updated'));

        return view('pages.detailed-listing', [
            'title' => trans('entities.recently_updated_pages'),
            'pages' => $pages
        ]);
    }

    /**
     * Show the view to choose a new parent to move a page into.
     * @throws NotFoundException
     */
    public function showMove(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('page-delete', $page);
        return view('pages.move', [
            'book' => $page->book,
            'page' => $page
        ]);
    }

    /**
     * Does the action of moving the location of a page.
     * @throws NotFoundException
     * @throws Throwable
     */
    public function move(Request $request, string $bookSlug, string $pageSlug)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('page-delete', $page);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($page->getUrl());
        }

        try {
            $parent = $this->newPageRepo->move($page, $entitySelection);
        } catch (Exception $exception) {
            if ($exception instanceof  PermissionsException) {
                $this->showPermissionError();
            }

            $this->showErrorNotification(trans('errors.selected_book_chapter_not_found'));
            return redirect()->back();
        }

        Activity::add($page, 'page_move', $page->book->id);
        $this->showSuccessNotification( trans('entities.pages_move_success', ['parentName' => $parent->name]));
        return redirect($page->getUrl());
    }

    /**
     * Show the view to copy a page.
     * @throws NotFoundException
     */
    public function showCopy(string $bookSlug, string $pageSlug)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('page-view', $page);
        session()->flashInput(['name' => $page->name]);
        return view('pages.copy', [
            'book' => $page->book,
            'page' => $page
        ]);
    }


    /**
     * Create a copy of a page within the requested target destination.
     * @throws NotFoundException
     * @throws Throwable
     */
    public function copy(Request $request, string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($pageSlug, $bookSlug);
        $this->checkOwnablePermission('page-view', $page);

        $entitySelection = $request->get('entity_selection', null) ?? null;
        $newName = $request->get('name', null);

        try {
            $pageCopy = $this->newPageRepo->copy($page, $entitySelection, $newName);
        } catch (Exception $exception) {
            if ($exception instanceof  PermissionsException) {
                $this->showPermissionError();
            }

            $this->showErrorNotification(trans('errors.selected_book_chapter_not_found'));
            return redirect()->back();
        }

        Activity::add($pageCopy, 'page_create', $pageCopy->book->id);

        $this->showSuccessNotification( trans('entities.pages_copy_success'));
        return redirect($pageCopy->getUrl());
    }

    /**
     * Show the Permissions view.
     * @throws NotFoundException
     */
    public function showPermissions(string $bookSlug, string $pageSlug)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('restrictions-manage', $page);
        $roles = $this->userRepo->getRestrictableRoles();
        return view('pages.permissions', [
            'page'  => $page,
            'roles' => $roles
        ]);
    }

    /**
     * Set the permissions for this page.
     * @throws NotFoundException
     * @throws Throwable
     */
    public function permissions(Request $request, string $bookSlug, string $pageSlug)
    {
        $page = $this->newPageRepo->getBySlug($bookSlug, $pageSlug);
        $this->checkOwnablePermission('restrictions-manage', $page);

        $restricted = $request->get('restricted') === 'true';
        $permissions = $request->filled('restrictions') ? collect($request->get('restrictions')) : null;
        $this->newPageRepo->updatePermissions($page, $restricted, $permissions);

        $this->showSuccessNotification( trans('entities.pages_permissions_success'));
        return redirect($page->getUrl());
    }
}

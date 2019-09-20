<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Repos\EntityRepo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Views;

class ChapterController extends Controller
{

    protected $userRepo;
    protected $entityRepo;

    /**
     * ChapterController constructor.
     * @param EntityRepo $entityRepo
     * @param UserRepo $userRepo
     */
    public function __construct(EntityRepo $entityRepo, UserRepo $userRepo)
    {
        $this->entityRepo = $entityRepo;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Show the form for creating a new chapter.
     * @param $bookSlug
     * @return Response
     */
    public function create($bookSlug)
    {
        $book = $this->entityRepo->getEntityBySlug('book', $bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);
        $this->setPageTitle(trans('entities.chapters_create'));
        return view('chapters.create', ['book' => $book, 'current' => $book]);
    }

    /**
     * Store a newly created chapter in storage.
     * @param Request $request
     * @param string $bookSlug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, string $bookSlug)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        $book = $this->entityRepo->getEntityBySlug('book', $bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);

        $input = $request->all();
        $input['priority'] = $this->entityRepo->getNewBookPriority($book);
        $chapter = $this->entityRepo->createFromInput('chapter', $input, $book);
        Activity::add($chapter, 'chapter_create', $book->id);
        return redirect($chapter->getUrl());
    }

    /**
     * Display the specified chapter.
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function show($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-view', $chapter);
        $sidebarTree = $this->entityRepo->getBookChildren($chapter->book);
        Views::add($chapter);
        $this->setPageTitle($chapter->getShortName());
        $pages = $this->entityRepo->getChapterChildren($chapter);
        return view('chapters.show', [
            'book' => $chapter->book,
            'chapter' => $chapter,
            'current' => $chapter,
            'sidebarTree' => $sidebarTree,
            'pages' => $pages
        ]);
    }

    /**
     * Show the form for editing the specified chapter.
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function edit($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->setPageTitle(trans('entities.chapters_edit_named', ['chapterName' => $chapter->getShortName()]));
        return view('chapters.edit', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Update the specified chapter in storage.
     * @param Request $request
     * @param string $bookSlug
     * @param string $chapterSlug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function update(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $this->entityRepo->updateFromInput($chapter, $request->all());
        Activity::add($chapter, 'chapter_update', $chapter->book->id);
        return redirect($chapter->getUrl());
    }

    /**
     * Shows the page to confirm deletion of this chapter.
     * @param $bookSlug
     * @param $chapterSlug
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);
        $this->setPageTitle(trans('entities.chapters_delete_named', ['chapterName' => $chapter->getShortName()]));
        return view('chapters.delete', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Remove the specified chapter from storage.
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function destroy($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $book = $chapter->book;
        $this->checkOwnablePermission('chapter-delete', $chapter);
        Activity::addMessage('chapter_delete', $chapter->name, $book->id);
        $this->entityRepo->destroyChapter($chapter);
        return redirect($book->getUrl());
    }

    /**
     * Show the page for moving a chapter.
     * @param $bookSlug
     * @param $chapterSlug
     * @return mixed
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showMove($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $this->setPageTitle(trans('entities.chapters_move_named', ['chapterName' => $chapter->getShortName()]));
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);
        return view('chapters.move', [
            'chapter' => $chapter,
            'book' => $chapter->book
        ]);
    }

    /**
     * Perform the move action for a chapter.
     * @param Request $request
     * @param string $bookSlug
     * @param string $chapterSlug
     * @return mixed
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function move(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($chapter->getUrl());
        }

        $stringExploded = explode(':', $entitySelection);
        $entityType = $stringExploded[0];
        $entityId = intval($stringExploded[1]);

        $parent = false;

        if ($entityType == 'book') {
            $parent = $this->entityRepo->getById('book', $entityId);
        }

        if ($parent === false || $parent === null) {
            $this->showErrorNotification( trans('errors.selected_book_not_found'));
            return redirect()->back();
        }

        $this->entityRepo->changeBook($chapter, $parent->id);
        $chapter->rebuildPermissions();

        Activity::add($chapter, 'chapter_move', $chapter->book->id);
        $this->showSuccessNotification( trans('entities.chapter_move_success', ['bookName' => $parent->name]));

        return redirect($chapter->getUrl());
    }

    /**
     * Show the Restrictions view.
     * @param $bookSlug
     * @param $chapterSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showPermissions($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);
        $roles = $this->userRepo->getRestrictableRoles();
        return view('chapters.permissions', [
            'chapter' => $chapter,
            'roles' => $roles
        ]);
    }

    /**
     * Set the restrictions for this chapter.
     * @param Request $request
     * @param string $bookSlug
     * @param string $chapterSlug
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Throwable
     */
    public function permissions(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->entityRepo->getEntityBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);
        $this->entityRepo->updateEntityPermissionsFromRequest($request, $chapter);
        $this->showSuccessNotification( trans('entities.chapters_permissions_success'));
        return redirect($chapter->getUrl());
    }
}

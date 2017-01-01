<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Repos\EntityRepo;
use BookStack\Repos\UserRepo;
use Illuminate\Http\Request;
use BookStack\Repos\BookRepo;
use BookStack\Repos\ChapterRepo;
use Illuminate\Http\Response;
use Views;

class ChapterController extends Controller
{

    protected $bookRepo;
    protected $chapterRepo;
    protected $userRepo;
    protected $entityRepo;

    /**
     * ChapterController constructor.
     * @param EntityRepo $entityRepo
     * @param BookRepo $bookRepo
     * @param ChapterRepo $chapterRepo
     * @param UserRepo $userRepo
     */
    public function __construct(EntityRepo $entityRepo, BookRepo $bookRepo, ChapterRepo $chapterRepo, UserRepo $userRepo)
    {
        $this->entityRepo = $entityRepo;
        // TODO - Remove below
        $this->bookRepo = $bookRepo;
        $this->chapterRepo = $chapterRepo;
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
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);
        $this->setPageTitle(trans('entities.chapters_create'));
        return view('chapters/create', ['book' => $book, 'current' => $book]);
    }

    /**
     * Store a newly created chapter in storage.
     * @param          $bookSlug
     * @param  Request $request
     * @return Response
     */
    public function store($bookSlug, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);

        $input = $request->all();
        $input['priority'] = $this->bookRepo->getNewPriority($book);
        $chapter = $this->chapterRepo->createFromInput($input, $book);
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
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-view', $chapter);
        $sidebarTree = $this->bookRepo->getChildren($chapter->book);
        Views::add($chapter);
        $this->setPageTitle($chapter->getShortName());
        $pages = $this->chapterRepo->getChildren($chapter);
        return view('chapters/show', [
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
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->setPageTitle(trans('entities.chapters_edit_named', ['chapterName' => $chapter->getShortName()]));
        return view('chapters/edit', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Update the specified chapter in storage.
     * @param  Request $request
     * @param          $bookSlug
     * @param          $chapterSlug
     * @return Response
     */
    public function update(Request $request, $bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        if ($chapter->name !== $request->get('name')) {
            $chapter->slug = $this->entityRepo->findSuitableSlug('chapter', $request->get('name'), $chapter->id, $chapter->book->id);
        }
        $chapter->fill($request->all());
        $chapter->updated_by = user()->id;
        $chapter->save();
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
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);
        $this->setPageTitle(trans('entities.chapters_delete_named', ['chapterName' => $chapter->getShortName()]));
        return view('chapters/delete', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Remove the specified chapter from storage.
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function destroy($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $book = $chapter->book;
        $this->checkOwnablePermission('chapter-delete', $chapter);
        Activity::addMessage('chapter_delete', $book->id, $chapter->name);
        $this->chapterRepo->destroy($chapter);
        return redirect($book->getUrl());
    }

    /**
     * Show the page for moving a chapter.
     * @param $bookSlug
     * @param $chapterSlug
     * @return mixed
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showMove($bookSlug, $chapterSlug) {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->setPageTitle(trans('entities.chapters_move_named', ['chapterName' => $chapter->getShortName()]));
        $this->checkOwnablePermission('chapter-update', $chapter);
        return view('chapters/move', [
            'chapter' => $chapter,
            'book' => $chapter->book
        ]);
    }

    /**
     * Perform the move action for a chapter.
     * @param $bookSlug
     * @param $chapterSlug
     * @param Request $request
     * @return mixed
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function move($bookSlug, $chapterSlug, Request $request) {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

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
            session()->flash('error', trans('errors.selected_book_not_found'));
            return redirect()->back();
        }

        $this->chapterRepo->changeBook($parent->id, $chapter, true);
        Activity::add($chapter, 'chapter_move', $chapter->book->id);
        session()->flash('success', trans('entities.chapter_move_success', ['bookName' => $parent->name]));

        return redirect($chapter->getUrl());
    }

    /**
     * Show the Restrictions view.
     * @param $bookSlug
     * @param $chapterSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRestrict($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);
        $roles = $this->userRepo->getRestrictableRoles();
        return view('chapters/restrictions', [
            'chapter' => $chapter,
            'roles' => $roles
        ]);
    }

    /**
     * Set the restrictions for this chapter.
     * @param $bookSlug
     * @param $chapterSlug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function restrict($bookSlug, $chapterSlug, Request $request)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);
        $this->chapterRepo->updateEntityPermissionsFromRequest($request, $chapter);
        session()->flash('success', trans('entities.chapters_permissions_success'));
        return redirect($chapter->getUrl());
    }
}

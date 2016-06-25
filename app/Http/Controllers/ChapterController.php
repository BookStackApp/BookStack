<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Repos\UserRepo;
use Illuminate\Http\Request;
use BookStack\Http\Requests;
use BookStack\Repos\BookRepo;
use BookStack\Repos\ChapterRepo;
use Views;

class ChapterController extends Controller
{

    protected $bookRepo;
    protected $chapterRepo;
    protected $userRepo;

    /**
     * ChapterController constructor.
     * @param BookRepo $bookRepo
     * @param ChapterRepo $chapterRepo
     * @param UserRepo $userRepo
     */
    public function __construct(BookRepo $bookRepo, ChapterRepo $chapterRepo, UserRepo $userRepo)
    {
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);
        $this->setPageTitle('Create New Chapter');
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

        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);

        $input = $request->all();
        $input['priority'] = $this->bookRepo->getNewPriority($book);
        $chapter = $this->chapterRepo->createFromInput($request->all(), $book);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        $this->checkOwnablePermission('chapter-view', $chapter);
        $sidebarTree = $this->bookRepo->getChildren($book);
        Views::add($chapter);
        $this->setPageTitle($chapter->getShortName());
        $pages = $this->chapterRepo->getChildren($chapter);
        return view('chapters/show', [
            'book' => $book,
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->setPageTitle('Edit Chapter' . $chapter->getShortName());
        return view('chapters/edit', ['book' => $book, 'chapter' => $chapter, 'current' => $chapter]);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $chapter->fill($request->all());
        $chapter->slug = $this->chapterRepo->findSuitableSlug($chapter->name, $book->id, $chapter->id);
        $chapter->updated_by = auth()->user()->id;
        $chapter->save();
        Activity::add($chapter, 'chapter_update', $book->id);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        $this->checkOwnablePermission('chapter-delete', $chapter);
        $this->setPageTitle('Delete Chapter' . $chapter->getShortName());
        return view('chapters/delete', ['book' => $book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Remove the specified chapter from storage.
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function destroy($bookSlug, $chapterSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        $this->checkOwnablePermission('chapter-update', $chapter);
        return view('chapters/move', [
            'chapter' => $chapter,
            'book' => $book
        ]);
    }

    public function move($bookSlug, $chapterSlug, Request $request) {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
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
            $parent = $this->bookRepo->getById($entityId);
        }

        if ($parent === false || $parent === null) {
            session()->flash('The selected Book was not found');
            return redirect()->back();
        }

        $this->chapterRepo->changeBook($parent->id, $chapter);
        Activity::add($chapter, 'chapter_move', $chapter->book->id);
        session()->flash('success', sprintf('Chapter moved to "%s"', $parent->name));

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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        $this->checkOwnablePermission('restrictions-manage', $chapter);
        $this->chapterRepo->updateEntityPermissionsFromRequest($request, $chapter);
        session()->flash('success', 'Chapter Restrictions Updated');
        return redirect($chapter->getUrl());
    }
}

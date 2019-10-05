<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Entities\Book;
use BookStack\Entities\Managers\BookContents;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;
use Views;

class ChapterController extends Controller
{

    protected $chapterRepo;

    /**
     * ChapterController constructor.
     */
    public function __construct(ChapterRepo $chapterRepo)
    {
        $this->chapterRepo = $chapterRepo;
        parent::__construct();
    }

    /**
     * Show the form for creating a new chapter.
     */
    public function create(string $bookSlug)
    {
        $book = Book::visible()->where('slug', '=', $bookSlug)->firstOrFail();
        $this->checkOwnablePermission('chapter-create', $book);

        $this->setPageTitle(trans('entities.chapters_create'));
        return view('chapters.create', ['book' => $book, 'current' => $book]);
    }

    /**
     * Store a newly created chapter in storage.
     * @throws ValidationException
     */
    public function store(Request $request, string $bookSlug)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        $book = Book::visible()->where('slug', '=', $bookSlug)->firstOrFail();
        $this->checkOwnablePermission('chapter-create', $book);

        $chapter = $this->chapterRepo->create($request->all(), $book);
        Activity::add($chapter, 'chapter_create', $book->id);

        return redirect($chapter->getUrl());
    }

    /**
     * Display the specified chapter.
     */
    public function show(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-view', $chapter);

        $sidebarTree = (new BookContents($chapter->book))->getTree();
        $pages = $chapter->getVisiblePages();
        Views::add($chapter);

        $this->setPageTitle($chapter->getShortName());
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
     */
    public function edit(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $this->setPageTitle(trans('entities.chapters_edit_named', ['chapterName' => $chapter->getShortName()]));
        return view('chapters.edit', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Update the specified chapter in storage.
     * @throws NotFoundException
     */
    public function update(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $this->chapterRepo->update($chapter, $request->all());
        Activity::add($chapter, 'chapter_update', $chapter->book->id);

        return redirect($chapter->getUrl());
    }

    /**
     * Shows the page to confirm deletion of this chapter.
     * @throws NotFoundException
     */
    public function showDelete(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $this->setPageTitle(trans('entities.chapters_delete_named', ['chapterName' => $chapter->getShortName()]));
        return view('chapters.delete', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Remove the specified chapter from storage.
     * @throws NotFoundException
     * @throws Throwable
     */
    public function destroy(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        Activity::addMessage('chapter_delete', $chapter->name, $chapter->book->id);
        $this->chapterRepo->destroy($chapter);

        return redirect($chapter->book->getUrl());
    }

    /**
     * Show the page for moving a chapter.
     * @throws NotFoundException
     */
    public function showMove(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
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
     * @throws NotFoundException
     */
    public function move(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($chapter->getUrl());
        }

        try {
            $newBook = $this->chapterRepo->move($chapter, $entitySelection);
        } catch (MoveOperationException $exception) {
            $this->showErrorNotification(trans('errors.selected_book_not_found'));
            return redirect()->back();
        }

        Activity::add($chapter, 'chapter_move', $newBook->id);

        $this->showSuccessNotification(trans('entities.chapter_move_success', ['bookName' => $newBook->name]));
        return redirect($chapter->getUrl());
    }

    /**
     * Show the Restrictions view.
     * @throws NotFoundException
     */
    public function showPermissions(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);

        return view('chapters.permissions', [
            'chapter' => $chapter,
        ]);
    }

    /**
     * Set the restrictions for this chapter.
     * @throws NotFoundException
     */
    public function permissions(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);

        $restricted = $request->get('restricted') === 'true';
        $permissions = $request->filled('restrictions') ? collect($request->get('restrictions')) : null;
        $this->chapterRepo->updatePermissions($chapter, $restricted, $permissions);

        $this->showSuccessNotification(trans('entities.chapters_permissions_success'));
        return redirect($chapter->getUrl());
    }
}

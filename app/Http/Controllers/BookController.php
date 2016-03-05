<?php

namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Repos\UserRepo;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use BookStack\Http\Requests;
use BookStack\Repos\BookRepo;
use BookStack\Repos\ChapterRepo;
use BookStack\Repos\PageRepo;
use Views;

class BookController extends Controller
{

    protected $bookRepo;
    protected $pageRepo;
    protected $chapterRepo;
    protected $userRepo;

    /**
     * BookController constructor.
     * @param BookRepo $bookRepo
     * @param PageRepo $pageRepo
     * @param ChapterRepo $chapterRepo
     * @param UserRepo $userRepo
     */
    public function __construct(BookRepo $bookRepo, PageRepo $pageRepo, ChapterRepo $chapterRepo, UserRepo $userRepo)
    {
        $this->bookRepo = $bookRepo;
        $this->pageRepo = $pageRepo;
        $this->chapterRepo = $chapterRepo;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the book.
     *
     * @return Response
     */
    public function index()
    {
        $books = $this->bookRepo->getAllPaginated(10);
        $recents = $this->signedIn ? $this->bookRepo->getRecentlyViewed(4, 0) : false;
        $popular = $this->bookRepo->getPopular(4, 0);
        $this->setPageTitle('Books');
        return view('books/index', ['books' => $books, 'recents' => $recents, 'popular' => $popular]);
    }

    /**
     * Show the form for creating a new book.
     *
     * @return Response
     */
    public function create()
    {
        $this->checkPermission('book-create-all');
        $this->setPageTitle('Create New Book');
        return view('books/create');
    }

    /**
     * Store a newly created book in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->checkPermission('book-create-all');
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000'
        ]);
        $book = $this->bookRepo->newFromInput($request->all());
        $book->slug = $this->bookRepo->findSuitableSlug($book->name);
        $book->created_by = Auth::user()->id;
        $book->updated_by = Auth::user()->id;
        $book->save();
        Activity::add($book, 'book_create', $book->id);
        return redirect($book->getUrl());
    }

    /**
     * Display the specified book.
     *
     * @param $slug
     * @return Response
     */
    public function show($slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $bookChildren = $this->bookRepo->getChildren($book);
        Views::add($book);
        $this->setPageTitle($book->getShortName());
        return view('books/show', ['book' => $book, 'current' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param $slug
     * @return Response
     */
    public function edit($slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->setPageTitle('Edit Book ' . $book->getShortName());
        return view('books/edit', ['book' => $book, 'current' => $book]);
    }

    /**
     * Update the specified book in storage.
     *
     * @param  Request $request
     * @param          $slug
     * @return Response
     */
    public function update(Request $request, $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000'
        ]);
        $book->fill($request->all());
        $book->slug = $this->bookRepo->findSuitableSlug($book->name, $book->id);
        $book->updated_by = Auth::user()->id;
        $book->save();
        Activity::add($book, 'book_update', $book->id);
        return redirect($book->getUrl());
    }

    /**
     * Shows the page to confirm deletion
     * @param $bookSlug
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        $this->setPageTitle('Delete Book ' . $book->getShortName());
        return view('books/delete', ['book' => $book, 'current' => $book]);
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     * @param string $bookSlug
     * @return \Illuminate\View\View
     */
    public function sort($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);
        $bookChildren = $this->bookRepo->getChildren($book);
        $books = $this->bookRepo->getAll(false);
        $this->setPageTitle('Sort Book ' . $book->getShortName());
        return view('books/sort', ['book' => $book, 'current' => $book, 'books' => $books, 'bookChildren' => $bookChildren]);
    }

    /**
     * Shows the sort box for a single book.
     * Used via AJAX when loading in extra books to a sort.
     * @param $bookSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSortItem($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $bookChildren = $this->bookRepo->getChildren($book);
        return view('books/sort-box', ['book' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Saves an array of sort mapping to pages and chapters.
     * @param  string $bookSlug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveSort($bookSlug, Request $request)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        // Return if no map sent
        if (!$request->has('sort-tree')) {
            return redirect($book->getUrl());
        }

        $sortedBooks = [];
        // Sort pages and chapters
        $sortMap = json_decode($request->get('sort-tree'));
        $defaultBookId = $book->id;
        foreach ($sortMap as $index => $bookChild) {
            $id = $bookChild->id;
            $isPage = $bookChild->type == 'page';
            $bookId = $this->bookRepo->exists($bookChild->book) ? $bookChild->book : $defaultBookId;
            $model = $isPage ? $this->pageRepo->getById($id) : $this->chapterRepo->getById($id);
            $isPage ? $this->pageRepo->changeBook($bookId, $model) : $this->chapterRepo->changeBook($bookId, $model);
            $model->priority = $index;
            if ($isPage) {
                $model->chapter_id = ($bookChild->parentChapter === false) ? 0 : $bookChild->parentChapter;
            }
            $model->save();
            if (!in_array($bookId, $sortedBooks)) {
                $sortedBooks[] = $bookId;
            }
        }

        // Add activity for books
        foreach ($sortedBooks as $bookId) {
            $updatedBook = $this->bookRepo->getById($bookId);
            Activity::add($updatedBook, 'book_sort', $updatedBook->id);
        }

        return redirect($book->getUrl());
    }

    /**
     * Remove the specified book from storage.
     * @param $bookSlug
     * @return Response
     */
    public function destroy($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        Activity::addMessage('book_delete', 0, $book->name);
        Activity::removeEntity($book);
        $this->bookRepo->destroyBySlug($bookSlug);
        return redirect('/books');
    }

    /**
     * Show the Restrictions view.
     * @param $bookSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRestrict($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);
        $roles = $this->userRepo->getRestrictableRoles();
        return view('books/restrictions', [
            'book' => $book,
            'roles' => $roles
        ]);
    }

    /**
     * Set the restrictions for this book.
     * @param $bookSlug
     * @param $bookSlug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function restrict($bookSlug, Request $request)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);
        $this->bookRepo->updateRestrictionsFromRequest($request, $book);
        session()->flash('success', 'Page Restrictions Updated');
        return redirect($book->getUrl());
    }
}

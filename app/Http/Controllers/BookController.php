<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\EntityContextManager;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\NotifyException;
use BookStack\Uploads\ImageRepo;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;
use Views;

class BookController extends Controller
{

    protected $bookRepo;
    protected $userRepo;
    protected $entityContextManager;
    protected $imageRepo;

    /**
     * BookController constructor.
     * @param BookRepo $bookRepo
     * @param UserRepo $userRepo
     * @param EntityContextManager $entityContextManager
     * @param ImageRepo $imageRepo
     */
    public function __construct(
        BookRepo $bookRepo,
        UserRepo $userRepo,
        EntityContextManager $entityContextManager,
        ImageRepo $imageRepo
    ) {
        $this->bookRepo = $bookRepo;
        $this->userRepo = $userRepo;
        $this->entityContextManager = $entityContextManager;
        $this->imageRepo = $imageRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the book.
     * @return Response
     */
    public function index()
    {
        $view = setting()->getForCurrentUser('books_view_type', config('app.views.books'));
        $sort = setting()->getForCurrentUser('books_sort', 'name');
        $order = setting()->getForCurrentUser('books_sort_order', 'asc');

        $books = $this->bookRepo->getAllPaginated('book', 18, $sort, $order);
        $recents = $this->isSignedIn() ? $this->bookRepo->getRecentlyViewed('book', 4, 0) : false;
        $popular = $this->bookRepo->getPopular('book', 4, 0);
        $new = $this->bookRepo->getRecentlyCreated('book', 4, 0);

        $this->entityContextManager->clearShelfContext();

        $this->setPageTitle(trans('entities.books'));
        return view('books.index', [
            'books' => $books,
            'recents' => $recents,
            'popular' => $popular,
            'new' => $new,
            'view' => $view,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    /**
     * Show the form for creating a new book.
     * @param string $shelfSlug
     * @return Response
     * @throws NotFoundException
     */
    public function create(string $shelfSlug = null)
    {
        $bookshelf = null;
        if ($shelfSlug !== null) {
            $bookshelf = $this->bookRepo->getEntityBySlug('bookshelf', $shelfSlug);
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        $this->checkPermission('book-create-all');
        $this->setPageTitle(trans('entities.books_create'));
        return view('books.create', [
            'bookshelf' => $bookshelf
        ]);
    }

    /**
     * Store a newly created book in storage.
     *
     * @param Request $request
     * @param string $shelfSlug
     * @return Response
     * @throws NotFoundException
     * @throws ImageUploadException
     * @throws ValidationException
     */
    public function store(Request $request, string $shelfSlug = null)
    {
        $this->checkPermission('book-create-all');
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image' => $this->imageRepo->getImageValidationRules(),
        ]);

        $bookshelf = null;
        if ($shelfSlug !== null) {
            /** @var Bookshelf $bookshelf */
            $bookshelf = $this->bookRepo->getEntityBySlug('bookshelf', $shelfSlug);
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        /** @var Book $book */
        $book = $this->bookRepo->createFromInput('book', $request->all());
        $this->bookUpdateActions($book, $request);
        Activity::add($book, 'book_create', $book->id);

        if ($bookshelf) {
            $bookshelf->appendBook($book);
            Activity::add($bookshelf, 'bookshelf_update');
        }

        return redirect($book->getUrl());
    }

    /**
     * Display the specified book.
     * @param Request $request
     * @param string $slug
     * @return Response
     * @throws NotFoundException
     */
    public function show(Request $request, string $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-view', $book);

        $bookChildren = $this->bookRepo->getBookChildren($book);

        Views::add($book);
        if ($request->has('shelf')) {
            $this->entityContextManager->setShelfContext(intval($request->get('shelf')));
        }

        $this->setPageTitle($book->getShortName());
        return view('books.show', [
            'book' => $book,
            'current' => $book,
            'bookChildren' => $bookChildren,
            'activity' => Activity::entityActivity($book, 20, 1)
        ]);
    }

    /**
     * Show the form for editing the specified book.
     * @param string $slug
     * @return Response
     * @throws NotFoundException
     */
    public function edit(string $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->setPageTitle(trans('entities.books_edit_named', ['bookName'=>$book->getShortName()]));
        return view('books.edit', ['book' => $book, 'current' => $book]);
    }

    /**
     * Update the specified book in storage.
     * @param Request $request
     * @param string $slug
     * @return Response
     * @throws ImageUploadException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, string $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image' => $this->imageRepo->getImageValidationRules(),
        ]);

         $book = $this->bookRepo->updateFromInput('book', $book, $request->all());
         $this->bookUpdateActions($book, $request);

         Activity::add($book, 'book_update', $book->id);

         return redirect($book->getUrl());
    }

    /**
     * Shows the page to confirm deletion
     * @param string $bookSlug
     * @return View
     * @throws NotFoundException
     */
    public function showDelete(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        $this->setPageTitle(trans('entities.books_delete_named', ['bookName' => $book->getShortName()]));
        return view('books.delete', ['book' => $book, 'current' => $book]);
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     * @param string $bookSlug
     * @return View
     * @throws NotFoundException
     */
    public function sort(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        $bookChildren = $this->bookRepo->getBookChildren($book, true);

        $this->setPageTitle(trans('entities.books_sort_named', ['bookName'=>$book->getShortName()]));
        return view('books.sort', ['book' => $book, 'current' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Shows the sort box for a single book.
     * Used via AJAX when loading in extra books to a sort.
     * @param string $bookSlug
     * @return Factory|View
     * @throws NotFoundException
     */
    public function sortItem(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $bookChildren = $this->bookRepo->getBookChildren($book);
        return view('books.sort-box', ['book' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Saves an array of sort mapping to pages and chapters.
     * @param Request $request
     * @param string $bookSlug
     * @return RedirectResponse|Redirector
     * @throws NotFoundException
     */
    public function saveSort(Request $request, string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        // Return if no map sent
        if (!$request->filled('sort-tree')) {
            return redirect($book->getUrl());
        }

        // Sort pages and chapters
        $sortMap = collect(json_decode($request->get('sort-tree')));
        $bookIdsInvolved = collect([$book->id]);

        // Load models into map
        $sortMap->each(function ($mapItem) use ($bookIdsInvolved) {
            $mapItem->type = ($mapItem->type === 'page' ? 'page' : 'chapter');
            $mapItem->model = $this->bookRepo->getById($mapItem->type, $mapItem->id);
            // Store source and target books
            $bookIdsInvolved->push(intval($mapItem->model->book_id));
            $bookIdsInvolved->push(intval($mapItem->book));
        });

        // Get the books involved in the sort
        $bookIdsInvolved = $bookIdsInvolved->unique()->toArray();
        $booksInvolved = $this->bookRepo->getManyById('book', $bookIdsInvolved, false, true);

        // Throw permission error if invalid ids or inaccessible books given.
        if (count($bookIdsInvolved) !== count($booksInvolved)) {
            $this->showPermissionError();
        }

        // Check permissions of involved books
        $booksInvolved->each(function (Book $book) {
             $this->checkOwnablePermission('book-update', $book);
        });

        // Perform the sort
        $sortMap->each(function ($mapItem) {
            $model = $mapItem->model;

            $priorityChanged = intval($model->priority) !== intval($mapItem->sort);
            $bookChanged = intval($model->book_id) !== intval($mapItem->book);
            $chapterChanged = ($mapItem->type === 'page') && intval($model->chapter_id) !== $mapItem->parentChapter;

            if ($bookChanged) {
                $this->bookRepo->changeBook($model, $mapItem->book);
            }
            if ($chapterChanged) {
                $model->chapter_id = intval($mapItem->parentChapter);
                $model->save();
            }
            if ($priorityChanged) {
                $model->priority = intval($mapItem->sort);
                $model->save();
            }
        });

        // Rebuild permissions and add activity for involved books.
        $booksInvolved->each(function (Book $book) {
            $book->rebuildPermissions();
            Activity::add($book, 'book_sort', $book->id);
        });

        return redirect($book->getUrl());
    }

    /**
     * Remove the specified book from storage.
     * @param string $bookSlug
     * @return Response
     * @throws NotFoundException
     * @throws Throwable
     * @throws NotifyException
     */
    public function destroy(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        Activity::addMessage('book_delete', $book->name);

        if ($book->cover) {
            $this->imageRepo->destroyImage($book->cover);
        }
        $this->bookRepo->destroyBook($book);

        return redirect('/books');
    }

    /**
     * Show the Restrictions view.
     * @param string $bookSlug
     * @return Factory|View
     * @throws NotFoundException
     */
    public function showPermissions(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);
        $roles = $this->userRepo->getRestrictableRoles();
        return view('books.permissions', [
            'book' => $book,
            'roles' => $roles
        ]);
    }

    /**
     * Set the restrictions for this book.
     * @param Request $request
     * @param string $bookSlug
     * @return RedirectResponse|Redirector
     * @throws NotFoundException
     * @throws Throwable
     */
    public function permissions(Request $request, string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);
        $this->bookRepo->updateEntityPermissionsFromRequest($request, $book);
        $this->showSuccessNotification(trans('entities.books_permissions_updated'));
        return redirect($book->getUrl());
    }

    /**
     * Common actions to run on book update.
     * Handles updating the cover image.
     * @param Book $book
     * @param Request $request
     * @throws ImageUploadException
     */
    protected function bookUpdateActions(Book $book, Request $request)
    {
        // Update the cover image if in request
        if ($request->has('image')) {
            $this->imageRepo->destroyImage($book->cover);
            $newImage = $request->file('image');
            $image = $this->imageRepo->saveNew($newImage, 'cover_book', $book->id, 512, 512, true);
            $book->image_id = $image->id;
            $book->save();
        }

        if ($request->has('image_reset')) {
            $this->imageRepo->destroyImage($book->cover);
            $book->image_id = 0;
            $book->save();
        }
    }
}

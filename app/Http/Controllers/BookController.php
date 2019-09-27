<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Actions\BookContents;
use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\EntityContextManager;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\NewBookRepo;
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
    protected $oldBookRepo;
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
        BookRepo $oldBookRepo,
        UserRepo $userRepo,
        EntityContextManager $entityContextManager,
        ImageRepo $imageRepo,
        NewBookRepo $bookRepo
    ) {
        $this->bookRepo = $bookRepo;
        $this->oldBookRepo = $oldBookRepo;
        $this->userRepo = $userRepo;
        $this->entityContextManager = $entityContextManager;
        $this->imageRepo = $imageRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the book.
     */
    public function index()
    {
        $view = setting()->getForCurrentUser('books_view_type', config('app.views.books'));
        $sort = setting()->getForCurrentUser('books_sort', 'name');
        $order = setting()->getForCurrentUser('books_sort_order', 'asc');

        $books = $this->bookRepo->getAllPaginated(18, $sort, $order);
        $recents = $this->isSignedIn() ? $this->bookRepo->getRecentlyViewed(4) : false;
        $popular = $this->bookRepo->getPopular(4);
        $new = $this->bookRepo->getRecentlyCreated(4);

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
     */
    public function create(string $shelfSlug = null)
    {
        $this->checkPermission('book-create-all');

        $bookshelf = null;
        if ($shelfSlug !== null) {
            $bookshelf = Bookshelf::visible()->where('slug', '=', $shelfSlug)->firstOrFail();
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        $this->setPageTitle(trans('entities.books_create'));
        return view('books.create', [
            'bookshelf' => $bookshelf
        ]);
    }

    /**
     * Store a newly created book in storage.
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
            $bookshelf = Bookshelf::visible()->where('slug', '=', $shelfSlug)->firstOrFail();
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        $book = $this->bookRepo->create($request->all(), $request->get('image', null));
        Activity::add($book, 'book_create', $book->id);

        if ($bookshelf) {
            $bookshelf->appendBook($book);
            Activity::add($bookshelf, 'bookshelf_update');
        }

        return redirect($book->getUrl());
    }

    /**
     * Display the specified book.
     */
    public function show(Request $request, string $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $bookChildren = (new BookContents($book))->getTree();

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
     */
    public function edit(string $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->setPageTitle(trans('entities.books_edit_named', ['bookName'=>$book->getShortName()]));
        return view('books.edit', ['book' => $book, 'current' => $book]);
    }

    /**
     * TODO - Continue from here
     */

    /**
     * Update the specified book in storage.
     * @param Request $request
     * @param string $slug
     * @return Response
     * @throws ImageUploadException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, string $slug)
    {
        $book = $this->oldBookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image' => $this->imageRepo->getImageValidationRules(),
        ]);

         $book = $this->oldBookRepo->updateFromInput($book, $request->all());
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
        $book = $this->oldBookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        $this->setPageTitle(trans('entities.books_delete_named', ['bookName' => $book->getShortName()]));
        return view('books.delete', ['book' => $book, 'current' => $book]);
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     * @param string $bookSlug
     * @return View
     */
    public function sort(string $bookSlug)
    {
        $book = $this->oldBookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        $bookChildren = (new BookContents($book))->getTree();

        $this->setPageTitle(trans('entities.books_sort_named', ['bookName'=>$book->getShortName()]));
        return view('books.sort', ['book' => $book, 'current' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Shows the sort box for a single book.
     * Used via AJAX when loading in extra books to a sort.
     * @param string $bookSlug
     * @return Factory|View
     */
    public function sortItem(string $bookSlug)
    {
        $book = $this->oldBookRepo->getBySlug($bookSlug);
        $bookChildren = (new BookContents($book))->getTree();
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
        $book = $this->oldBookRepo->getBySlug($bookSlug);
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
            $mapItem->model = $this->oldBookRepo->getById($mapItem->type, $mapItem->id);
            // Store source and target books
            $bookIdsInvolved->push(intval($mapItem->model->book_id));
            $bookIdsInvolved->push(intval($mapItem->book));
        });

        // Get the books involved in the sort
        $bookIdsInvolved = $bookIdsInvolved->unique()->toArray();
        $booksInvolved = $this->oldBookRepo->getManyById('book', $bookIdsInvolved, false, true);

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
                $this->oldBookRepo->changeBook($model, $mapItem->book);
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
        $book = $this->oldBookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        Activity::addMessage('book_delete', $book->name);

        if ($book->cover) {
            $this->imageRepo->destroyImage($book->cover);
        }
        $this->oldBookRepo->destroyBook($book);

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
        $book = $this->oldBookRepo->getBySlug($bookSlug);
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
        $book = $this->oldBookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);
        $this->oldBookRepo->updateEntityPermissionsFromRequest($request, $book);
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

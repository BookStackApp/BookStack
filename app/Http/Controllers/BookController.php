<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Book;
use BookStack\Entities\EntityContextManager;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Entities\ExportService;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Views;

class BookController extends Controller
{

    protected $entityRepo;
    protected $userRepo;
    protected $exportService;
    protected $entityContextManager;
    protected $imageRepo;

    /**
     * BookController constructor.
     * @param EntityRepo $entityRepo
     * @param UserRepo $userRepo
     * @param ExportService $exportService
     * @param EntityContextManager $entityContextManager
     * @param ImageRepo $imageRepo
     */
    public function __construct(
        EntityRepo $entityRepo,
        UserRepo $userRepo,
        ExportService $exportService,
        EntityContextManager $entityContextManager,
        ImageRepo $imageRepo
    ) {
        $this->entityRepo = $entityRepo;
        $this->userRepo = $userRepo;
        $this->exportService = $exportService;
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
        $view = setting()->getUser($this->currentUser, 'books_view_type', config('app.views.books'));
        $sort = setting()->getUser($this->currentUser, 'books_sort', 'name');
        $order = setting()->getUser($this->currentUser, 'books_sort_order', 'asc');
        $sortOptions = [
            'name' => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ];

        $books = $this->entityRepo->getAllPaginated('book', 18, $sort, $order);
        $recents = $this->signedIn ? $this->entityRepo->getRecentlyViewed('book', 4, 0) : false;
        $popular = $this->entityRepo->getPopular('book', 4, 0);
        $new = $this->entityRepo->getRecentlyCreated('book', 4, 0);

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
            'sortOptions' => $sortOptions,
        ]);
    }

    /**
     * Show the form for creating a new book.
     * @param string $shelfSlug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function create(string $shelfSlug = null)
    {
        $bookshelf = null;
        if ($shelfSlug !== null) {
            $bookshelf = $this->entityRepo->getBySlug('bookshelf', $shelfSlug);
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
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \BookStack\Exceptions\ImageUploadException
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
            $bookshelf = $this->entityRepo->getBySlug('bookshelf', $shelfSlug);
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        $book = $this->entityRepo->createFromInput('book', $request->all());
        $this->bookUpdateActions($book, $request);
        Activity::add($book, 'book_create', $book->id);

        if ($bookshelf) {
            $this->entityRepo->appendBookToShelf($bookshelf, $book);
            Activity::add($bookshelf, 'bookshelf_update');
        }

        return redirect($book->getUrl());
    }

    /**
     * Display the specified book.
     * @param $slug
     * @param Request $request
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function show($slug, Request $request)
    {
        $book = $this->entityRepo->getBySlug('book', $slug);
        $this->checkOwnablePermission('book-view', $book);

        $bookChildren = $this->entityRepo->getBookChildren($book);

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
     * @param $slug
     * @return Response
     */
    public function edit($slug)
    {
        $book = $this->entityRepo->getBySlug('book', $slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->setPageTitle(trans('entities.books_edit_named', ['bookName'=>$book->getShortName()]));
        return view('books.edit', ['book' => $book, 'current' => $book]);
    }

    /**
     * Update the specified book in storage.
     * @param Request $request
     * @param          $slug
     * @return Response
     * @throws \BookStack\Exceptions\ImageUploadException
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function update(Request $request, string $slug)
    {
        $book = $this->entityRepo->getBySlug('book', $slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image' => $this->imageRepo->getImageValidationRules(),
        ]);

         $book = $this->entityRepo->updateFromInput('book', $book, $request->all());
         $this->bookUpdateActions($book, $request);

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
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        $this->setPageTitle(trans('entities.books_delete_named', ['bookName'=>$book->getShortName()]));
        return view('books.delete', ['book' => $book, 'current' => $book]);
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     * @param string $bookSlug
     * @return \Illuminate\View\View
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function sort($bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        $bookChildren = $this->entityRepo->getBookChildren($book, true);

        $this->setPageTitle(trans('entities.books_sort_named', ['bookName'=>$book->getShortName()]));
        return view('books.sort', ['book' => $book, 'current' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Shows the sort box for a single book.
     * Used via AJAX when loading in extra books to a sort.
     * @param $bookSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSortItem($bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $bookChildren = $this->entityRepo->getBookChildren($book);
        return view('books.sort-box', ['book' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Saves an array of sort mapping to pages and chapters.
     * @param  string $bookSlug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveSort($bookSlug, Request $request)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
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
            $mapItem->model = $this->entityRepo->getById($mapItem->type, $mapItem->id);
            // Store source and target books
            $bookIdsInvolved->push(intval($mapItem->model->book_id));
            $bookIdsInvolved->push(intval($mapItem->book));
        });

        // Get the books involved in the sort
        $bookIdsInvolved = $bookIdsInvolved->unique()->toArray();
        $booksInvolved = $this->entityRepo->getManyById('book', $bookIdsInvolved, false, true);
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
                $this->entityRepo->changeBook($mapItem->type, $mapItem->book, $model);
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
            $this->entityRepo->buildJointPermissionsForBook($book);
            Activity::add($book, 'book_sort', $book->id);
        });

        return redirect($book->getUrl());
    }

    /**
     * Remove the specified book from storage.
     * @param $bookSlug
     * @return Response
     */
    public function destroy($bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        Activity::addMessage('book_delete', 0, $book->name);

        if ($book->cover) {
            $this->imageRepo->destroyImage($book->cover);
        }
        $this->entityRepo->destroyBook($book);

        return redirect('/books');
    }

    /**
     * Show the Restrictions view.
     * @param $bookSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPermissions($bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);
        $roles = $this->userRepo->getRestrictableRoles();
        return view('books.permissions', [
            'book' => $book,
            'roles' => $roles
        ]);
    }

    /**
     * Set the restrictions for this book.
     * @param $bookSlug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Throwable
     */
    public function permissions($bookSlug, Request $request)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);
        $this->entityRepo->updateEntityPermissionsFromRequest($request, $book);
        session()->flash('success', trans('entities.books_permissions_updated'));
        return redirect($book->getUrl());
    }

    /**
     * Export a book as a PDF file.
     * @param string $bookSlug
     * @return mixed
     */
    public function exportPdf($bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $pdfContent = $this->exportService->bookToPdf($book);
        return $this->downloadResponse($pdfContent, $bookSlug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     * @param string $bookSlug
     * @return mixed
     */
    public function exportHtml($bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $htmlContent = $this->exportService->bookToContainedHtml($book);
        return $this->downloadResponse($htmlContent, $bookSlug . '.html');
    }

    /**
     * Export a book as a plain text file.
     * @param $bookSlug
     * @return mixed
     */
    public function exportPlainText($bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $textContent = $this->exportService->bookToPlainText($book);
        return $this->downloadResponse($textContent, $bookSlug . '.txt');
    }

    /**
     * Common actions to run on book update.
     * Handles updating the cover image.
     * @param Book $book
     * @param Request $request
     * @throws \BookStack\Exceptions\ImageUploadException
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

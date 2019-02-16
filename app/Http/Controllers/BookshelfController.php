<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Entities\ExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Views;

class BookshelfController extends Controller
{

    protected $entityRepo;
    protected $userRepo;
    protected $exportService;

    /**
     * BookController constructor.
     * @param \BookStack\Entities\Repos\EntityRepo $entityRepo
     * @param UserRepo $userRepo
     * @param \BookStack\Entities\ExportService $exportService
     */
    public function __construct(EntityRepo $entityRepo, UserRepo $userRepo, ExportService $exportService)
    {
        $this->entityRepo = $entityRepo;
        $this->userRepo = $userRepo;
        $this->exportService = $exportService;
        parent::__construct();
    }

    /**
     * Display a listing of the book.
     * @return Response
     */
    public function index()
    {

        $view = setting()->getUser($this->currentUser, 'bookshelves_view_type', config('app.views.bookshelves', 'grid'));

        $sort = setting()->getUser($this->currentUser, 'bookshelves_sort', 'name');
        $order = setting()->getUser($this->currentUser, 'bookshelves_sort_order', 'asc');
        $sortOptions = [
            'name' => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ];

        $shelves = $this->entityRepo->getAllPaginated('bookshelf', 18, $sort, $order, function($query) {
            $query->with(['books']);
        });
        $recents = $this->signedIn ? $this->entityRepo->getRecentlyViewed('bookshelf', 4, 0) : false;
        $popular = $this->entityRepo->getPopular('bookshelf', 4, 0);
        $new = $this->entityRepo->getRecentlyCreated('bookshelf', 4, 0);


        $this->setPageTitle(trans('entities.shelves'));
        return view('shelves.index', [
            'shelves' => $shelves,
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
     * Show the form for creating a new bookshelf.
     * @return Response
     */
    public function create()
    {
        $this->checkPermission('bookshelf-create-all');
        $books = $this->entityRepo->getAll('book', false, 'update');
        $this->setPageTitle(trans('entities.shelves_create'));
        return view('shelves.create', ['books' => $books]);
    }

    /**
     * Store a newly created bookshelf in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->checkPermission('bookshelf-create-all');
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
        ]);

        $bookshelf = $this->entityRepo->createFromInput('bookshelf', $request->all());
        $this->entityRepo->updateShelfBooks($bookshelf, $request->get('books', ''));
        Activity::add($bookshelf, 'bookshelf_create');

        return redirect($bookshelf->getUrl());
    }


    /**
     * Display the specified bookshelf.
     * @param String $slug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function show(string $slug)
    {
        $bookshelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $bookshelf Bookshelf */
        $this->checkOwnablePermission('book-view', $bookshelf);

        $books = $this->entityRepo->getBookshelfChildren($bookshelf);
        Views::add($bookshelf);

        $this->setPageTitle($bookshelf->getShortName());
        return view('shelves.show', [
            'shelf' => $bookshelf,
            'books' => $books,
            'activity' => Activity::entityActivity($bookshelf, 20, 1)
        ]);
    }

    /**
     * Show the form for editing the specified bookshelf.
     * @param $slug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function edit(string $slug)
    {
        $bookshelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $bookshelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-update', $bookshelf);

        $shelfBooks = $this->entityRepo->getBookshelfChildren($bookshelf);
        $shelfBookIds = $shelfBooks->pluck('id');
        $books = $this->entityRepo->getAll('book', false, 'update');
        $books = $books->filter(function ($book) use ($shelfBookIds) {
             return !$shelfBookIds->contains($book->id);
        });

        $this->setPageTitle(trans('entities.shelves_edit_named', ['name' => $bookshelf->getShortName()]));
        return view('shelves.edit', [
            'shelf' => $bookshelf,
            'books' => $books,
            'shelfBooks' => $shelfBooks,
        ]);
    }


    /**
     * Update the specified bookshelf in storage.
     * @param  Request $request
     * @param string $slug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function update(Request $request, string $slug)
    {
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $bookshelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-update', $shelf);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
        ]);

         $shelf = $this->entityRepo->updateFromInput('bookshelf', $shelf, $request->all());
         $this->entityRepo->updateShelfBooks($shelf, $request->get('books', ''));
         Activity::add($shelf, 'bookshelf_update');

         return redirect($shelf->getUrl());
    }


    /**
     * Shows the page to confirm deletion
     * @param $slug
     * @return \Illuminate\View\View
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showDelete(string $slug)
    {
        $bookshelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $bookshelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-delete', $bookshelf);

        $this->setPageTitle(trans('entities.shelves_delete_named', ['name' => $bookshelf->getShortName()]));
        return view('shelves.delete', ['shelf' => $bookshelf]);
    }

    /**
     * Remove the specified bookshelf from storage.
     * @param string $slug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Throwable
     */
    public function destroy(string $slug)
    {
        $bookshelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $bookshelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-delete', $bookshelf);
        Activity::addMessage('bookshelf_delete', 0, $bookshelf->name);
        $this->entityRepo->destroyBookshelf($bookshelf);
        return redirect('/shelves');
    }

    /**
     * Show the permissions view.
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showPermissions(string $slug)
    {
        $bookshelf = $this->entityRepo->getBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $bookshelf);

        $roles = $this->userRepo->getRestrictableRoles();
        return view('shelves.permissions', [
            'shelf' => $bookshelf,
            'roles' => $roles
        ]);
    }

    /**
     * Set the permissions for this bookshelf.
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Throwable
     */
    public function permissions(string $slug, Request $request)
    {
        $bookshelf = $this->entityRepo->getBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $bookshelf);

        $this->entityRepo->updateEntityPermissionsFromRequest($request, $bookshelf);
        session()->flash('success', trans('entities.shelves_permissions_updated'));
        return redirect($bookshelf->getUrl());
    }

    /**
     * Copy the permissions of a bookshelf to the child books.
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function copyPermissions(string $slug)
    {
        $bookshelf = $this->entityRepo->getBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $bookshelf);

        $updateCount = $this->entityRepo->copyBookshelfPermissions($bookshelf);
        session()->flash('success', trans('entities.shelves_copy_permission_success', ['count' => $updateCount]));
        return redirect($bookshelf->getUrl());
    }
}

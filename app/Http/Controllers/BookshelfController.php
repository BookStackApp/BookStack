<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\EntityContextManager;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Views;

class BookshelfController extends Controller
{

    protected $entityRepo;
    protected $userRepo;
    protected $entityContextManager;
    protected $imageRepo;

    /**
     * BookController constructor.
     * @param EntityRepo $entityRepo
     * @param UserRepo $userRepo
     * @param EntityContextManager $entityContextManager
     * @param ImageRepo $imageRepo
     */
    public function __construct(EntityRepo $entityRepo, UserRepo $userRepo, EntityContextManager $entityContextManager, ImageRepo $imageRepo)
    {
        $this->entityRepo = $entityRepo;
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
        $view = setting()->getForCurrentUser('bookshelves_view_type', config('app.views.bookshelves', 'grid'));
        $sort = setting()->getForCurrentUser('bookshelves_sort', 'name');
        $order = setting()->getForCurrentUser('bookshelves_sort_order', 'asc');
        $sortOptions = [
            'name' => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ];

        $shelves = $this->entityRepo->getAllPaginated('bookshelf', 18, $sort, $order);
        foreach ($shelves as $shelf) {
            $shelf->books = $this->entityRepo->getBookshelfChildren($shelf);
        }

        $recents = $this->isSignedIn() ? $this->entityRepo->getRecentlyViewed('bookshelf', 4, 0) : false;
        $popular = $this->entityRepo->getPopular('bookshelf', 4, 0);
        $new = $this->entityRepo->getRecentlyCreated('bookshelf', 4, 0);

        $this->entityContextManager->clearShelfContext();
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
     * @param Request $request
     * @return Response
     * @throws \BookStack\Exceptions\ImageUploadException
     */
    public function store(Request $request)
    {
        $this->checkPermission('bookshelf-create-all');
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image' => $this->imageRepo->getImageValidationRules(),
        ]);

        $shelf = $this->entityRepo->createFromInput('bookshelf', $request->all());
        $this->shelfUpdateActions($shelf, $request);

        Activity::add($shelf, 'bookshelf_create');
        return redirect($shelf->getUrl());
    }


    /**
     * Display the specified bookshelf.
     * @param String $slug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function show(string $slug)
    {
        /** @var Bookshelf $shelf */
        $shelf = $this->entityRepo->getEntityBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('book-view', $shelf);

        $books = $this->entityRepo->getBookshelfChildren($shelf);
        Views::add($shelf);
        $this->entityContextManager->setShelfContext($shelf->id);

        $this->setPageTitle($shelf->getShortName());

        return view('shelves.show', [
            'shelf' => $shelf,
            'books' => $books,
            'activity' => Activity::entityActivity($shelf, 20, 1)
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
        $shelf = $this->entityRepo->getEntityBySlug('bookshelf', $slug); /** @var $shelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-update', $shelf);

        $shelfBooks = $this->entityRepo->getBookshelfChildren($shelf);
        $shelfBookIds = $shelfBooks->pluck('id');
        $books = $this->entityRepo->getAll('book', false, 'update');
        $books = $books->filter(function ($book) use ($shelfBookIds) {
             return !$shelfBookIds->contains($book->id);
        });

        $this->setPageTitle(trans('entities.shelves_edit_named', ['name' => $shelf->getShortName()]));
        return view('shelves.edit', [
            'shelf' => $shelf,
            'books' => $books,
            'shelfBooks' => $shelfBooks,
        ]);
    }


    /**
     * Update the specified bookshelf in storage.
     * @param Request $request
     * @param string $slug
     * @return Response
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \BookStack\Exceptions\ImageUploadException
     */
    public function update(Request $request, string $slug)
    {
        $shelf = $this->entityRepo->getEntityBySlug('bookshelf', $slug); /** @var $bookshelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-update', $shelf);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image' => $this->imageRepo->getImageValidationRules(),
        ]);

         $shelf = $this->entityRepo->updateFromInput($shelf, $request->all());
         $this->shelfUpdateActions($shelf, $request);

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
        $shelf = $this->entityRepo->getEntityBySlug('bookshelf', $slug); /** @var $shelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        $this->setPageTitle(trans('entities.shelves_delete_named', ['name' => $shelf->getShortName()]));
        return view('shelves.delete', ['shelf' => $shelf]);
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
        $shelf = $this->entityRepo->getEntityBySlug('bookshelf', $slug); /** @var $shelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-delete', $shelf);
        Activity::addMessage('bookshelf_delete', $shelf->name);

        if ($shelf->cover) {
            $this->imageRepo->destroyImage($shelf->cover);
        }
        $this->entityRepo->destroyBookshelf($shelf);

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
        $shelf = $this->entityRepo->getEntityBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $roles = $this->userRepo->getRestrictableRoles();
        return view('shelves.permissions', [
            'shelf' => $shelf,
            'roles' => $roles
        ]);
    }

    /**
     * Set the permissions for this bookshelf.
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Throwable
     */
    public function permissions(Request $request, string $slug)
    {
        $shelf = $this->entityRepo->getEntityBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $this->entityRepo->updateEntityPermissionsFromRequest($request, $shelf);
        $this->showSuccessNotification( trans('entities.shelves_permissions_updated'));
        return redirect($shelf->getUrl());
    }

    /**
     * Copy the permissions of a bookshelf to the child books.
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function copyPermissions(string $slug)
    {
        $shelf = $this->entityRepo->getEntityBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $updateCount = $this->entityRepo->copyBookshelfPermissions($shelf);
        $this->showSuccessNotification( trans('entities.shelves_copy_permission_success', ['count' => $updateCount]));
        return redirect($shelf->getUrl());
    }

    /**
     * Common actions to run on bookshelf update.
     * @param Bookshelf $shelf
     * @param Request $request
     * @throws \BookStack\Exceptions\ImageUploadException
     */
    protected function shelfUpdateActions(Bookshelf $shelf, Request $request)
    {
        // Update the books that the shelf references
        $this->entityRepo->updateShelfBooks($shelf, $request->get('books', ''));

        // Update the cover image if in request
        if ($request->has('image')) {
            $newImage = $request->file('image');
            $this->imageRepo->destroyImage($shelf->cover);
            $image = $this->imageRepo->saveNew($newImage, 'cover_shelf', $shelf->id, 512, 512, true);
            $shelf->image_id = $image->id;
            $shelf->save();
        }

        if ($request->has('image_reset')) {
            $this->imageRepo->destroyImage($shelf->cover);
            $shelf->image_id = 0;
            $shelf->save();
        }
    }
}

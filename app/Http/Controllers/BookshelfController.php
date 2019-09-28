<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Book;
use BookStack\Entities\Managers\EntityContext;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Uploads\ImageRepo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Views;

class BookshelfController extends Controller
{

    protected $bookshelfRepo;
    protected $userRepo;
    protected $entityContextManager;
    protected $imageRepo;

    /**
     * BookController constructor.
     */
    public function __construct(BookshelfRepo $bookshelfRepo, UserRepo $userRepo, EntityContext $entityContextManager, ImageRepo $imageRepo)
    {
        $this->bookshelfRepo = $bookshelfRepo;
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
        $view = setting()->getForCurrentUser('bookshelves_view_type', config('app.views.bookshelves', 'grid'));
        $sort = setting()->getForCurrentUser('bookshelves_sort', 'name');
        $order = setting()->getForCurrentUser('bookshelves_sort_order', 'asc');
        $sortOptions = [
            'name' => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ];

        $shelves = $this->bookshelfRepo->getAllPaginated(18, $sort, $order);
        $recents = $this->isSignedIn() ? $this->bookshelfRepo->getRecentlyViewed(4) : false;
        $popular = $this->bookshelfRepo->getPopular(4);
        $new = $this->bookshelfRepo->getRecentlyCreated(4);

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
     */
    public function create()
    {
        $this->checkPermission('bookshelf-create-all');
        $books = Book::hasPermission('update')->all();
        $this->setPageTitle(trans('entities.shelves_create'));
        return view('shelves.create', ['books' => $books]);
    }

    /**
     * Store a newly created bookshelf in storage.
     * @throws ValidationException
     * @throws ImageUploadException
     */
    public function store(Request $request)
    {
        $this->checkPermission('bookshelf-create-all');
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image' => $this->getImageValidationRules(),
        ]);

        $bookIds = explode(',', $request->get('books', ''));
        $shelf = $this->bookshelfRepo->create($request->all(), $bookIds);
        $this->bookshelfRepo->updateCoverImage($shelf);

        Activity::add($shelf, 'bookshelf_create');
        return redirect($shelf->getUrl());
    }

    /**
     * Display the bookshelf of the given slug.
     */
    public function show(string $slug)
    {
        $shelf = $this->bookshelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-view', $shelf);

        Views::add($shelf);
        $this->entityContextManager->setShelfContext($shelf->id);

        $this->setPageTitle($shelf->getShortName());
        return view('shelves.show', [
            'shelf' => $shelf,
            'activity' => Activity::entityActivity($shelf, 20, 1)
        ]);
    }

    /**
     * Show the form for editing the specified bookshelf.
     */
    public function edit(string $slug)
    {
        $shelf = $this->bookshelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-update', $shelf);

        $shelfBookIds = $shelf->books()->get(['id'])->pluck('id');
        $books = Book::hasPermission('update')->whereNotIn('id', $shelfBookIds);

        $this->setPageTitle(trans('entities.shelves_edit_named', ['name' => $shelf->getShortName()]));
        return view('shelves.edit', [
            'shelf' => $shelf,
            'books' => $books,
        ]);
    }

    /**
     * Update the specified bookshelf in storage.
     * @throws ValidationException
     * @throws ImageUploadException
     */
    public function update(Request $request, string $slug)
    {
        $shelf = $this->bookshelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-update', $shelf);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image' => $this->imageRepo->getImageValidationRules(),
        ]);


        $bookIds = explode(',', $request->get('books', ''));
        $shelf = $this->bookshelfRepo->update($shelf, $request->all(), $bookIds);
        $this->bookshelfRepo->updateCoverImage($shelf);
        Activity::add($shelf, 'bookshelf_update');

        return redirect($shelf->getUrl());
    }


    /**
     * Shows the page to confirm deletion
     */
    public function showDelete(string $slug)
    {
        $shelf = $this->bookshelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        $this->setPageTitle(trans('entities.shelves_delete_named', ['name' => $shelf->getShortName()]));
        return view('shelves.delete', ['shelf' => $shelf]);
    }

    /**
     * Remove the specified bookshelf from storage.
     * @throws Exception
     */
    public function destroy(string $slug)
    {
        $shelf = $this->bookshelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        Activity::addMessage('bookshelf_delete', $shelf->name);
        $this->bookshelfRepo->destroy($shelf);

        return redirect('/shelves');
    }

    /**
     * Show the permissions view.
     */
    public function showPermissions(string $slug)
    {
        $shelf = $this->bookshelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $roles = $this->userRepo->getRestrictableRoles();
        return view('shelves.permissions', [
            'shelf' => $shelf,
            'roles' => $roles
        ]);
    }

    /**
     * Set the permissions for this bookshelf.
     */
    public function permissions(Request $request, string $slug)
    {
        $shelf = $this->bookshelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $restricted = $request->get('restricted') === 'true';
        $permissions = $request->filled('restrictions') ? collect($request->get('restrictions')) : null;
        $this->bookshelfRepo->updatePermissions($shelf, $restricted, $permissions);

        $this->showSuccessNotification( trans('entities.shelves_permissions_updated'));
        return redirect($shelf->getUrl());
    }

    /**
     * Copy the permissions of a bookshelf to the child books.
     */
    public function copyPermissions(string $slug)
    {
        $shelf = $this->bookshelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $updateCount = $this->bookshelfRepo->copyDownPermissions($shelf);
        $this->showSuccessNotification( trans('entities.shelves_copy_permission_success', ['count' => $updateCount]));
        return redirect($shelf->getUrl());
    }

}

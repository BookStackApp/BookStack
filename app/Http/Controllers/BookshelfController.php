<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityQueries;
use BookStack\Actions\View;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Entities\Tools\PermissionsUpdater;
use BookStack\Entities\Tools\ShelfContext;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookshelfController extends Controller
{
    protected BookshelfRepo $shelfRepo;
    protected ShelfContext $shelfContext;

    public function __construct(BookshelfRepo $shelfRepo, ShelfContext $shelfContext)
    {
        $this->shelfRepo = $shelfRepo;
        $this->shelfContext = $shelfContext;
    }

    /**
     * Display a listing of the book.
     */
    public function index()
    {
        $view = setting()->getForCurrentUser('bookshelves_view_type');
        $sort = setting()->getForCurrentUser('bookshelves_sort', 'name');
        $order = setting()->getForCurrentUser('bookshelves_sort_order', 'asc');
        $sortOptions = [
            'name'       => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ];

        $shelves = $this->shelfRepo->getAllPaginated(18, $sort, $order);
        $recents = $this->isSignedIn() ? $this->shelfRepo->getRecentlyViewed(4) : false;
        $popular = $this->shelfRepo->getPopular(4);
        $new = $this->shelfRepo->getRecentlyCreated(4);

        $this->shelfContext->clearShelfContext();
        $this->setPageTitle(trans('entities.shelves'));

        return view('shelves.index', [
            'shelves'     => $shelves,
            'recents'     => $recents,
            'popular'     => $popular,
            'new'         => $new,
            'view'        => $view,
            'sort'        => $sort,
            'order'       => $order,
            'sortOptions' => $sortOptions,
        ]);
    }

    /**
     * Show the form for creating a new bookshelf.
     */
    public function create()
    {
        $this->checkPermission('bookshelf-create-all');
        $books = Book::visible()->orderBy('name')->get(['name', 'id', 'slug']);
        $this->setPageTitle(trans('entities.shelves_create'));

        return view('shelves.create', ['books' => $books]);
    }

    /**
     * Store a newly created bookshelf in storage.
     *
     * @throws ValidationException
     * @throws ImageUploadException
     */
    public function store(Request $request)
    {
        $this->checkPermission('bookshelf-create-all');
        $validated = $this->validate($request, [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
            'tags'        => ['array'],
        ]);

        $bookIds = explode(',', $request->get('books', ''));
        $shelf = $this->shelfRepo->create($validated, $bookIds);

        return redirect($shelf->getUrl());
    }

    /**
     * Display the bookshelf of the given slug.
     *
     * @throws NotFoundException
     */
    public function show(ActivityQueries $activities, string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-view', $shelf);

        $sort = setting()->getForCurrentUser('shelf_books_sort', 'default');
        $order = setting()->getForCurrentUser('shelf_books_sort_order', 'asc');

        $sortedVisibleShelfBooks = $shelf->visibleBooks()->get()
            ->sortBy($sort === 'default' ? 'pivot.order' : $sort, SORT_REGULAR, $order === 'desc')
            ->values()
            ->all();

        View::incrementFor($shelf);
        $this->shelfContext->setShelfContext($shelf->id);
        $view = setting()->getForCurrentUser('bookshelf_view_type');

        $this->setPageTitle($shelf->getShortName());

        return view('shelves.show', [
            'shelf'                   => $shelf,
            'sortedVisibleShelfBooks' => $sortedVisibleShelfBooks,
            'view'                    => $view,
            'activity'                => $activities->entityActivity($shelf, 20, 1),
            'order'                   => $order,
            'sort'                    => $sort,
        ]);
    }

    /**
     * Show the form for editing the specified bookshelf.
     */
    public function edit(string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-update', $shelf);

        $shelfBookIds = $shelf->books()->get(['id'])->pluck('id');
        $books = Book::visible()->whereNotIn('id', $shelfBookIds)->orderBy('name')->get(['name', 'id', 'slug']);

        $this->setPageTitle(trans('entities.shelves_edit_named', ['name' => $shelf->getShortName()]));

        return view('shelves.edit', [
            'shelf' => $shelf,
            'books' => $books,
        ]);
    }

    /**
     * Update the specified bookshelf in storage.
     *
     * @throws ValidationException
     * @throws ImageUploadException
     * @throws NotFoundException
     */
    public function update(Request $request, string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-update', $shelf);
        $validated = $this->validate($request, [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
            'tags'        => ['array'],
        ]);

        if ($request->has('image_reset')) {
            $validated['image'] = null;
        } elseif (array_key_exists('image', $validated) && is_null($validated['image'])) {
            unset($validated['image']);
        }

        $bookIds = explode(',', $request->get('books', ''));
        $shelf = $this->shelfRepo->update($shelf, $validated, $bookIds);

        return redirect($shelf->getUrl());
    }

    /**
     * Shows the page to confirm deletion.
     */
    public function showDelete(string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        $this->setPageTitle(trans('entities.shelves_delete_named', ['name' => $shelf->getShortName()]));

        return view('shelves.delete', ['shelf' => $shelf]);
    }

    /**
     * Remove the specified bookshelf from storage.
     *
     * @throws Exception
     */
    public function destroy(string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        $this->shelfRepo->destroy($shelf);

        return redirect('/shelves');
    }

    /**
     * Show the permissions view.
     */
    public function showPermissions(string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        return view('shelves.permissions', [
            'shelf' => $shelf,
        ]);
    }

    /**
     * Set the permissions for this bookshelf.
     */
    public function permissions(Request $request, PermissionsUpdater $permissionsUpdater, string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $permissionsUpdater->updateFromPermissionsForm($shelf, $request);

        $this->showSuccessNotification(trans('entities.shelves_permissions_updated'));

        return redirect($shelf->getUrl());
    }

    /**
     * Copy the permissions of a bookshelf to the child books.
     */
    public function copyPermissions(string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $updateCount = $this->shelfRepo->copyDownPermissions($shelf);
        $this->showSuccessNotification(trans('entities.shelves_copy_permission_success', ['count' => $updateCount]));

        return redirect($shelf->getUrl());
    }
}

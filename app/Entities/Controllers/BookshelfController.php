<?php

namespace BookStack\Entities\Controllers;

use BookStack\Activity\ActivityQueries;
use BookStack\Activity\Models\View;
use BookStack\Entities\Queries\BookQueries;
use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Entities\Tools\ShelfContext;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controller;
use BookStack\References\ReferenceFetcher;
use BookStack\Util\SimpleListOptions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class BookshelfController extends Controller
{
    public function __construct(
        protected BookshelfRepo $shelfRepo,
        protected BookshelfQueries $queries,
        protected BookQueries $bookQueries,
        protected ShelfContext $shelfContext,
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    /**
     * Display a listing of bookshelves.
     */
    public function index(Request $request)
    {
        $view = setting()->getForCurrentUser('bookshelves_view_type');
        $listOptions = SimpleListOptions::fromRequest($request, 'bookshelves')->withSortOptions([
            'name'       => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ]);

        $shelves = $this->queries->visibleForListWithCover()
            ->orderBy($listOptions->getSort(), $listOptions->getOrder())
            ->paginate(18);
        $recents = $this->isSignedIn() ? $this->queries->recentlyViewedForCurrentUser()->get() : false;
        $popular = $this->queries->popularForList()->get();
        $new = $this->queries->visibleForList()
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $this->shelfContext->clearShelfContext();
        $this->setPageTitle(trans('entities.shelves'));

        return view('shelves.index', [
            'shelves'     => $shelves,
            'recents'     => $recents,
            'popular'     => $popular,
            'new'         => $new,
            'view'        => $view,
            'listOptions' => $listOptions,
        ]);
    }

    /**
     * Show the form for creating a new bookshelf.
     */
    public function create()
    {
        $this->checkPermission('bookshelf-create-all');
        $books = $this->bookQueries->visibleForList()->orderBy('name')->get(['name', 'id', 'slug', 'created_at', 'updated_at']);
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
            'name'             => ['required', 'string', 'max:255'],
            'description_html' => ['string', 'max:2000'],
            'image'            => array_merge(['nullable'], $this->getImageValidationRules()),
            'tags'             => ['array'],
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
    public function show(Request $request, ActivityQueries $activities, string $slug)
    {
        $shelf = $this->queries->findVisibleBySlugOrFail($slug);
        $this->checkOwnablePermission('bookshelf-view', $shelf);

        $listOptions = SimpleListOptions::fromRequest($request, 'shelf_books')->withSortOptions([
            'default' => trans('common.sort_default'),
            'name' => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ]);

        $sort = $listOptions->getSort();
        $sortedVisibleShelfBooks = $shelf->visibleBooks()
            ->reorder($sort === 'default' ? 'order' : $sort, $listOptions->getOrder())
            ->get()
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
            'listOptions'             => $listOptions,
            'referenceCount'          => $this->referenceFetcher->getReferenceCountToEntity($shelf),
        ]);
    }

    /**
     * Show the form for editing the specified bookshelf.
     */
    public function edit(string $slug)
    {
        $shelf = $this->queries->findVisibleBySlugOrFail($slug);
        $this->checkOwnablePermission('bookshelf-update', $shelf);

        $shelfBookIds = $shelf->books()->get(['id'])->pluck('id');
        $books = $this->bookQueries->visibleForList()
            ->whereNotIn('id', $shelfBookIds)
            ->orderBy('name')
            ->get(['name', 'id', 'slug', 'created_at', 'updated_at']);

        $this->setPageTitle(trans('entities.shelves_edit_named', ['name' => $shelf->getShortName()]));

        return view('shelves.edit', [
            'shelf' => $shelf,
            'books' => $books,
            'edit_perms' => userCan('restrictions-manage', $shelf),
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
        $shelf = $this->queries->findVisibleBySlugOrFail($slug);
        $this->checkOwnablePermission('bookshelf-update', $shelf);
        $validated = $this->validate($request, [
            'name'                    => ['required', 'string', 'max:255'],
            'description_html'        => ['string', 'max:2000'],
            'image'                   => array_merge(['nullable'], $this->getImageValidationRules()),
            'tags'                    => ['array'],
            'new_books_inherit_perms' => ['string', Rule::in(['true', 'false', 'null'])],
        ]);

        if ($request->has('image_reset')) {
            $validated['image'] = null;
        } elseif (array_key_exists('image', $validated) && is_null($validated['image'])) {
            unset($validated['image']);
        }

        if ($request->has('new_books_inherit_perms')) {
            $this->checkOwnablePermission('restrictions-manage', $shelf);
            $p = $validated['new_books_inherit_perms'];
            $validated['new_books_inherit_perms'] = $p == "true" ? true : ($p == "false" ? false : null);
        } else {
            unset($validated['new_books_inherit_perms']);
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
        $shelf = $this->queries->findVisibleBySlugOrFail($slug);
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
        $shelf = $this->queries->findVisibleBySlugOrFail($slug);
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        $this->shelfRepo->destroy($shelf);

        return redirect('/shelves');
    }
}

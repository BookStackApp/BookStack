<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityQueries;
use BookStack\Actions\View;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Entities\Tools\ShelfContext;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\References\ReferenceFetcher;
use BookStack\Util\SimpleListOptions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookshelfController extends Controller
{
    protected BookshelfRepo $shelfRepo;
    protected ShelfContext $shelfContext;
    protected ReferenceFetcher $referenceFetcher;

    public function __construct(BookshelfRepo $shelfRepo, ShelfContext $shelfContext, ReferenceFetcher $referenceFetcher)
    {
        $this->shelfRepo = $shelfRepo;
        $this->shelfContext = $shelfContext;
        $this->referenceFetcher = $referenceFetcher;
    }

    /**
     * Display a listing of the book.
     */
    public function index(Request $request)
    {
        $view = setting()->getForCurrentUser('bookshelves_view_type');
        $listOptions = SimpleListOptions::fromRequest($request, 'bookshelves')->withSortOptions([
            'name'       => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ]);

        $shelves = $this->shelfRepo->getAllPaginated(18, $listOptions->getSort(), $listOptions->getOrder());
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
            'listOptions' => $listOptions,
        ]);
    }

    /**
     * Show the form for creating a new bookshelf.
     */
    public function create()
    {
        $this->checkPermission('bookshelf-create-all');
        $books = Book::visible()->orderBy('name')->get(['name', 'id', 'slug', 'created_at', 'updated_at']);
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
    public function show(Request $request, ActivityQueries $activities, string $slug)
    {
        $shelf = $this->shelfRepo->getBySlug($slug);
        $this->checkOwnablePermission('bookshelf-view', $shelf);

        $listOptions = SimpleListOptions::fromRequest($request, 'shelf_books')->withSortOptions([
            'default' => trans('common.sort_default'),
            'name' => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ]);

        $sort = $listOptions->getSort();
        $sortedVisibleShelfBooks = $shelf->visibleBooks()->get()
            ->sortBy($sort === 'default' ? 'pivot.order' : $sort, SORT_REGULAR, $listOptions->getOrder() === 'desc')
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
            'referenceCount'          => $this->referenceFetcher->getPageReferenceCountToEntity($shelf),
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
        $books = Book::visible()->whereNotIn('id', $shelfBookIds)->orderBy('name')->get(['name', 'id', 'slug', 'created_at', 'updated_at']);

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
}

<?php

namespace BookStack\Entities\Controllers;

use BookStack\Activity\ActivityQueries;
use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\View;
use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Queries\RecordQueries;
use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\Entities\Repos\RecordRepo;
use BookStack\Entities\Tools\RecordContents;
use BookStack\Entities\Tools\Cloner;
use BookStack\Entities\Tools\HierarchyTransformer;
use BookStack\Entities\Tools\ShelfContext;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Facades\Activity;
use BookStack\Http\Controller;
use BookStack\References\ReferenceFetcher;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class RecordController extends Controller
{
    public function __construct(
        protected ShelfContext $shelfContext,
        protected RecordRepo $recordRepo,
        protected RecordQueries $queries,
        protected BookshelfQueries $shelfQueries,
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    /**
     * Display a listing of the record.
     */
    public function index(Request $request)
    {
        $view = setting()->getForCurrentUser('books_view_type');
        // dd($view);
        $listOptions = SimpleListOptions::fromRequest($request, 'records')->withSortOptions([
            'name' => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ]);
        // dd($listOptions);

        $records = $this->queries->visibleForListWithCover()
            ->orderBy($listOptions->getSort(), $listOptions->getOrder())
            ->paginate(18);
        $recents = $this->isSignedIn() ? $this->queries->recentlyViewedForCurrentUser()->take(4)->get() : false;
        $popular = $this->queries->popularForList()->take(4)->get();
        $new = $this->queries->visibleForList()->orderBy('created_at', 'desc')->take(4)->get();

        $this->shelfContext->clearShelfContext();

        $this->setPageTitle(trans('entities.records'));

        return view('records.index', [
            'records' => $records,
            'recents' => $recents,
            'popular' => $popular,
            'new' => $new,
            'view' => $view,
            'listOptions' => $listOptions,
        ]);
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(?string $shelfSlug = null)
    {
        $this->checkPermission('book-create-all');

        $bookshelf = null;
        if ($shelfSlug !== null) {
            $bookshelf = $this->shelfQueries->findVisibleBySlugOrFail($shelfSlug);
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        $this->setPageTitle(trans('entities.records_create'));

        return view('records.create', [
            'recordshelf' => $bookshelf,
        ]);
    }

    /**
     * Store a newly created book in storage.
     *
     * @throws ImageUploadException
     * @throws ValidationException
     */
    public function store(Request $request, ?string $shelfSlug = null)
    {
        $this->checkPermission('book-create-all');
        $validated = $this->validate($request, [
            'name'                => ['required', 'string', 'max:255'],
            'description_html'    => ['string', 'max:2000'],
            'image'               => array_merge(['nullable'], $this->getImageValidationRules()),
            'tags'                => ['array'],
            'default_template_id' => ['nullable', 'integer'],
        ]);

        $bookshelf = null;
        if ($shelfSlug !== null) {
            $bookshelf = $this->shelfQueries->findVisibleBySlugOrFail($shelfSlug);
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        $book = $this->recordRepo->create($validated);

        if ($bookshelf) {
            $bookshelf->appendRecord($book);
            Activity::add(ActivityType::RECORDSHELF_UPDATE, $bookshelf);
        }

        return redirect($book->getUrl());
    }

    /**
     * Display the specified book.
     */
    public function show(Request $request, ActivityQueries $activities, string $slug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($slug);
        
        $bookChildren = (new RecordContents($book))->getTree(true);
        // dd('pp');
        $bookParentShelves = $book->shelves()->scopes('visible')->get();
        
        View::incrementFor($book);
        if ($request->has('shelf')) {
            $this->shelfContext->setShelfContext(intval($request->get('shelf')));
        }

        $this->setPageTitle($book->getShortName());
// dd($book->getUrl());
        return view('books.show', [
            'book'              => $book,
            'current'           => $book,
            'bookChildren'      => $bookChildren,
            'bookParentShelves' => $bookParentShelves,
            'watchOptions'      => new UserEntityWatchOptions(user(), $book),
            'activity'          => $activities->entityActivity($book, 20, 1),
            'referenceCount'    => $this->referenceFetcher->getReferenceCountToEntity($book),
        ]);
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(string $slug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->setPageTitle(trans('entities.books_edit_named', ['bookName' => $book->getShortName()]));

        return view('books.edit', ['book' => $book, 'current' => $book]);
    }

    /**
     * Update the specified book in storage.
     *
     * @throws ImageUploadException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, string $slug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($slug);
        $this->checkOwnablePermission('book-update', $book);

        $validated = $this->validate($request, [
            'name'                => ['required', 'string', 'max:255'],
            'description_html'    => ['string', 'max:2000'],
            'image'               => array_merge(['nullable'], $this->getImageValidationRules()),
            'tags'                => ['array'],
            'default_template_id' => ['nullable', 'integer'],
        ]);

        if ($request->has('image_reset')) {
            $validated['image'] = null;
        } elseif (array_key_exists('image', $validated) && is_null($validated['image'])) {
            unset($validated['image']);
        }

        $book = $this->recordRepo->update($book, $validated);

        return redirect($book->getUrl());
    }

    /**
     * Shows the page to confirm deletion.
     */
    public function showDelete(string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        $this->setPageTitle(trans('entities.books_delete_named', ['bookName' => $book->getShortName()]));

        return view('books.delete', ['book' => $book, 'current' => $book]);
    }

    /**
     * Remove the specified book from the system.
     *
     * @throws Throwable
     */
    public function destroy(string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);

        $this->recordRepo->destroy($book);

        return redirect('/books');
    }

    /**
     * Show the view to copy a book.
     *
     * @throws NotFoundException
     */
    public function showCopy(string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('book-view', $book);

        session()->flashInput(['name' => $book->name]);

        return view('books.copy', [
            'book' => $book,
        ]);
    }

    /**
     * Create a copy of a book within the requested target destination.
     *
     * @throws NotFoundException
     */
    public function copy(Request $request, Cloner $cloner, string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('book-view', $book);
        $this->checkPermission('book-create-all');

        $newName = $request->get('name') ?: $book->name;
        $bookCopy = $cloner->cloneRecord($book, $newName);
        $this->showSuccessNotification(trans('entities.books_copy_success'));

        return redirect($bookCopy->getUrl());
    }

    /**
     * Convert the chapter to a book.
     */
    public function convertToShelf(HierarchyTransformer $transformer, string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('book-update', $book);
        $this->checkOwnablePermission('book-delete', $book);
        $this->checkPermission('bookshelf-create-all');
        $this->checkPermission('book-create-all');

        $shelf = $transformer->transformRecordToShelf($book);

        return redirect($shelf->getUrl());
    }
}

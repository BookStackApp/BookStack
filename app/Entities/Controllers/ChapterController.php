<?php

namespace BookStack\Entities\Controllers;

use BookStack\Activity\Models\View;
use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Queries\ChapterQueries;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\Cloner;
use BookStack\Entities\Tools\HierarchyTransformer;
use BookStack\Entities\Tools\NextPreviousContentLocator;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\NotifyException;
use BookStack\Exceptions\PermissionsException;
use BookStack\Http\Controller;
use BookStack\References\ReferenceFetcher;
use BookStack\Util\DatabaseTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class ChapterController extends Controller
{
    public function __construct(
        protected ChapterRepo $chapterRepo,
        protected ChapterQueries $queries,
        protected EntityQueries $entityQueries,
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    /**
     * Show the form for creating a new chapter.
     */
    public function create(string $bookSlug)
    {
        $book = $this->entityQueries->books->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);

        $this->setPageTitle(trans('entities.chapters_create'));

        return view('chapters.create', [
            'book' => $book,
            'current' => $book,
        ]);
    }

    /**
     * Store a newly created chapter in storage.
     *
     * @throws ValidationException
     */
    public function store(Request $request, string $bookSlug)
    {
        $validated = $this->validate($request, [
            'name'                => ['required', 'string', 'max:255'],
            'description_html'    => ['string', 'max:2000'],
            'tags'                => ['array'],
            'default_template_id' => ['nullable', 'integer'],
        ]);

        $book = $this->entityQueries->books->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);

        $chapter = $this->chapterRepo->create($validated, $book);

        return redirect($chapter->getUrl());
    }

    /**
     * Display the specified chapter.
     */
    public function show(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-view', $chapter);

        $sidebarTree = (new BookContents($chapter->book))->getTree();
        $pages = $this->entityQueries->pages->visibleForChapterList($chapter->id)->get();

        $nextPreviousLocator = new NextPreviousContentLocator($chapter, $sidebarTree);
        View::incrementFor($chapter);

        $this->setPageTitle($chapter->getShortName());

        return view('chapters.show', [
            'book'           => $chapter->book,
            'chapter'        => $chapter,
            'current'        => $chapter,
            'sidebarTree'    => $sidebarTree,
            'watchOptions'   => new UserEntityWatchOptions(user(), $chapter),
            'pages'          => $pages,
            'next'           => $nextPreviousLocator->getNext(),
            'previous'       => $nextPreviousLocator->getPrevious(),
            'referenceCount' => $this->referenceFetcher->getReferenceCountToEntity($chapter),
        ]);
    }

    /**
     * Show the form for editing the specified chapter.
     */
    public function edit(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $this->setPageTitle(trans('entities.chapters_edit_named', ['chapterName' => $chapter->getShortName()]));

        return view('chapters.edit', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Update the specified chapter in storage.
     *
     * @throws NotFoundException
     */
    public function update(Request $request, string $bookSlug, string $chapterSlug)
    {
        $validated = $this->validate($request, [
            'name'                => ['required', 'string', 'max:255'],
            'description_html'    => ['string', 'max:2000'],
            'tags'                => ['array'],
            'default_template_id' => ['nullable', 'integer'],
        ]);

        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $this->chapterRepo->update($chapter, $validated);

        return redirect($chapter->getUrl());
    }

    /**
     * Shows the page to confirm deletion of this chapter.
     *
     * @throws NotFoundException
     */
    public function showDelete(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $this->setPageTitle(trans('entities.chapters_delete_named', ['chapterName' => $chapter->getShortName()]));

        return view('chapters.delete', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Remove the specified chapter from storage.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function destroy(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $this->chapterRepo->destroy($chapter);

        return redirect($chapter->book->getUrl());
    }

    /**
     * Show the page for moving a chapter.
     *
     * @throws NotFoundException
     */
    public function showMove(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->setPageTitle(trans('entities.chapters_move_named', ['chapterName' => $chapter->getShortName()]));
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        return view('chapters.move', [
            'chapter' => $chapter,
            'book'    => $chapter->book,
        ]);
    }

    /**
     * Perform the move action for a chapter.
     *
     * @throws NotFoundException|NotifyException
     */
    public function move(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($chapter->getUrl());
        }

        try {
            $this->chapterRepo->move($chapter, $entitySelection);
        } catch (PermissionsException $exception) {
            $this->showPermissionError();
        } catch (MoveOperationException $exception) {
            $this->showErrorNotification(trans('errors.selected_book_not_found'));

            return redirect($chapter->getUrl('/move'));
        }

        return redirect($chapter->getUrl());
    }

    /**
     * Show the view to copy a chapter.
     *
     * @throws NotFoundException
     */
    public function showCopy(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-view', $chapter);

        session()->flashInput(['name' => $chapter->name]);

        return view('chapters.copy', [
            'book'    => $chapter->book,
            'chapter' => $chapter,
        ]);
    }

    /**
     * Create a copy of a chapter within the requested target destination.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function copy(Request $request, Cloner $cloner, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-view', $chapter);

        $entitySelection = $request->get('entity_selection') ?: null;
        $newParentBook = $entitySelection ? $this->entityQueries->findVisibleByStringIdentifier($entitySelection) : $chapter->getParent();

        if (!$newParentBook instanceof Book) {
            $this->showErrorNotification(trans('errors.selected_book_not_found'));

            return redirect($chapter->getUrl('/copy'));
        }

        $this->checkOwnablePermission('chapter-create', $newParentBook);

        $newName = $request->get('name') ?: $chapter->name;
        $chapterCopy = $cloner->cloneChapter($chapter, $newParentBook, $newName);
        $this->showSuccessNotification(trans('entities.chapters_copy_success'));

        return redirect($chapterCopy->getUrl());
    }

    /**
     * Convert the chapter to a book.
     */
    public function convertToBook(HierarchyTransformer $transformer, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);
        $this->checkPermission('book-create-all');

        $book = (new DatabaseTransaction(function () use ($chapter, $transformer) {
            return $transformer->transformChapterToBook($chapter);
        }))->run();

        return redirect($book->getUrl());
    }
}

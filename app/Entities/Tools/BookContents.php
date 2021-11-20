<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\SortOperationException;
use Illuminate\Support\Collection;

class BookContents
{
    /**
     * @var Book
     */
    protected $book;

    /**
     * BookContents constructor.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Get the current priority of the last item
     * at the top-level of the book.
     */
    public function getLastPriority(): int
    {
        $maxPage = Page::visible()->where('book_id', '=', $this->book->id)
            ->where('draft', '=', false)
            ->where('chapter_id', '=', 0)->max('priority');
        $maxChapter = Chapter::visible()->where('book_id', '=', $this->book->id)
            ->max('priority');

        return max($maxChapter, $maxPage, 1);
    }

    /**
     * Get the contents as a sorted collection tree.
     */
    public function getTree(bool $showDrafts = false, bool $renderPages = false): Collection
    {
        $pages = $this->getPages($showDrafts, $renderPages);
        $chapters = Chapter::visible()->where('book_id', '=', $this->book->id)->get();
        $all = collect()->concat($pages)->concat($chapters);
        $chapterMap = $chapters->keyBy('id');
        $lonePages = collect();

        $pages->groupBy('chapter_id')->each(function ($pages, $chapter_id) use ($chapterMap, &$lonePages) {
            $chapter = $chapterMap->get($chapter_id);
            if ($chapter) {
                $chapter->setAttribute('visible_pages', collect($pages)->sortBy($this->bookChildSortFunc()));
            } else {
                $lonePages = $lonePages->concat($pages);
            }
        });

        $chapters->whereNull('visible_pages')->each(function (Chapter $chapter) {
            $chapter->setAttribute('visible_pages', collect([]));
        });

        $all->each(function (Entity $entity) use ($renderPages) {
            $entity->setRelation('book', $this->book);

            if ($renderPages && $entity instanceof Page) {
                $entity->html = (new PageContent($entity))->render();
            }
        });

        return collect($chapters)->concat($lonePages)->sortBy($this->bookChildSortFunc());
    }

    /**
     * Function for providing a sorting score for an entity in relation to the
     * other items within the book.
     */
    protected function bookChildSortFunc(): callable
    {
        return function (Entity $entity) {
            if (isset($entity['draft']) && $entity['draft']) {
                return -100;
            }

            return $entity['priority'] ?? 0;
        };
    }

    /**
     * Get the visible pages within this book.
     */
    protected function getPages(bool $showDrafts = false, bool $getPageContent = false): Collection
    {
        $query = Page::visible()
            ->select($getPageContent ? Page::$contentAttributes : Page::$listAttributes)
            ->where('book_id', '=', $this->book->id);

        if (!$showDrafts) {
            $query->where('draft', '=', false);
        }

        return $query->get();
    }

    /**
     * Sort the books content using the given map.
     * The map is a single-dimension collection of objects in the following format:
     *   {
     *     +"id": "294" (ID of item)
     *     +"sort": 1 (Sort order index)
     *     +"parentChapter": false (ID of parent chapter, as string, or false)
     *     +"type": "page" (Entity type of item)
     *     +"book": "1" (Id of book to place item in)
     *   }.
     *
     * Returns a list of books that were involved in the operation.
     *
     * @throws SortOperationException
     */
    public function sortUsingMap(Collection $sortMap): Collection
    {
        // Load models into map
        $this->loadModelsIntoSortMap($sortMap);
        $booksInvolved = $this->getBooksInvolvedInSort($sortMap);

        // Perform the sort
        $sortMap->each(function ($mapItem) {
            $this->applySortUpdates($mapItem);
        });

        // Update permissions and activity.
        $booksInvolved->each(function (Book $book) {
            $book->rebuildPermissions();
        });

        return $booksInvolved;
    }

    /**
     * Using the given sort map item, detect changes for the related model
     * and update it if required.
     */
    protected function applySortUpdates(\stdClass $sortMapItem)
    {
        /** @var BookChild $model */
        $model = $sortMapItem->model;

        $priorityChanged = intval($model->priority) !== intval($sortMapItem->sort);
        $bookChanged = intval($model->book_id) !== intval($sortMapItem->book);
        $chapterChanged = ($model instanceof Page) && intval($model->chapter_id) !== $sortMapItem->parentChapter;

        if ($bookChanged) {
            $model->changeBook($sortMapItem->book);
        }

        if ($chapterChanged) {
            $model->chapter_id = intval($sortMapItem->parentChapter);
            $model->save();
        }

        if ($priorityChanged) {
            $model->priority = intval($sortMapItem->sort);
            $model->save();
        }
    }

    /**
     * Load models from the database into the given sort map.
     */
    protected function loadModelsIntoSortMap(Collection $sortMap): void
    {
        $keyMap = $sortMap->keyBy(function (\stdClass $sortMapItem) {
            return  $sortMapItem->type . ':' . $sortMapItem->id;
        });
        $pageIds = $sortMap->where('type', '=', 'page')->pluck('id');
        $chapterIds = $sortMap->where('type', '=', 'chapter')->pluck('id');

        $pages = Page::visible()->whereIn('id', $pageIds)->get();
        $chapters = Chapter::visible()->whereIn('id', $chapterIds)->get();

        foreach ($pages as $page) {
            $sortItem = $keyMap->get('page:' . $page->id);
            $sortItem->model = $page;
        }

        foreach ($chapters as $chapter) {
            $sortItem = $keyMap->get('chapter:' . $chapter->id);
            $sortItem->model = $chapter;
        }
    }

    /**
     * Get the books involved in a sort.
     * The given sort map should have its models loaded first.
     *
     * @throws SortOperationException
     */
    protected function getBooksInvolvedInSort(Collection $sortMap): Collection
    {
        $bookIdsInvolved = collect([$this->book->id]);
        $bookIdsInvolved = $bookIdsInvolved->concat($sortMap->pluck('book'));
        $bookIdsInvolved = $bookIdsInvolved->concat($sortMap->pluck('model.book_id'));
        $bookIdsInvolved = $bookIdsInvolved->unique()->toArray();

        $books = Book::hasPermission('update')->whereIn('id', $bookIdsInvolved)->get();

        if (count($books) !== count($bookIdsInvolved)) {
            throw new SortOperationException('Could not find all books requested in sort operation');
        }

        return $books;
    }
}

<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
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
     * Sort the books content using the given sort map.
     * Returns a list of books that were involved in the operation.
     *
     * @returns Book[]
     */
    public function sortUsingMap(BookSortMap $sortMap): array
    {
        // Load models into map
        $modelMap = $this->loadModelsFromSortMap($sortMap);

        // Perform the sort
        foreach ($sortMap->all() as $item) {
            $this->applySortUpdates($item, $modelMap);
        }

        /** @var Book[] $booksInvolved */
        $booksInvolved = array_values(array_filter($modelMap, function (string $key) {
            return strpos($key, 'book:') === 0;
        }, ARRAY_FILTER_USE_KEY));

        // Update permissions of books involved
        foreach ($booksInvolved as $book) {
            $book->rebuildPermissions();
        }

        return $booksInvolved;
    }

    /**
     * Using the given sort map item, detect changes for the related model
     * and update it if required. Changes where permissions are lacking will
     * be skipped and not throw an error.
     *
     * @param array<string, Entity> $modelMap
     */
    protected function applySortUpdates(BookSortMapItem $sortMapItem, array $modelMap): void
    {
        /** @var BookChild $model */
        $model = $modelMap[$sortMapItem->type . ':' . $sortMapItem->id] ?? null;
        if (!$model) {
            return;
        }

        $priorityChanged = $model->priority !== $sortMapItem->sort;
        $bookChanged = $model->book_id !== $sortMapItem->parentBookId;
        $chapterChanged = ($model instanceof Page) && $model->chapter_id !== $sortMapItem->parentChapterId;

        // Stop if there's no change
        if (!$priorityChanged && !$bookChanged && !$chapterChanged) {
            return;
        }

        $currentParentKey =  'book:' . $model->book_id;
        if ($model instanceof Page && $model->chapter_id) {
             $currentParentKey = 'chapter:' . $model->chapter_id;
        }

        $currentParent = $modelMap[$currentParentKey];
        /** @var Book $newBook */
        $newBook = $modelMap['book:' . $sortMapItem->parentBookId] ?? null;
        /** @var ?Chapter $newChapter */
        $newChapter = $sortMapItem->parentChapterId ? ($modelMap['chapter:' . $sortMapItem->parentChapterId] ?? null) : null;

        // Check permissions of our changes to be made
        if (!$currentParent || !$newBook) {
            return;
        } else if (!userCan('chapter-update', $currentParent) && !userCan('book-update', $currentParent)) {
            return;
        } else if ($bookChanged && !$newChapter && !userCan('book-update', $newBook)) {
            return;
        } else if ($newChapter && !userCan('chapter-update', $newChapter)) {
            return;
        } else if (($chapterChanged || $bookChanged) && $newChapter && $newBook->id !== $newChapter->book_id) {
            return;
        }

        // Action the required changes
        if ($bookChanged) {
            $model->changeBook($sortMapItem->parentBookId);
        }

        if ($chapterChanged) {
            $model->chapter_id = $sortMapItem->parentChapterId ?? 0;
        }

        if ($priorityChanged) {
            $model->priority = $sortMapItem->sort;
        }

        if ($chapterChanged || $priorityChanged) {
            $model->save();
        }
    }

    /**
     * Load models from the database into the given sort map.
     * @return array<string, Entity>
     */
    protected function loadModelsFromSortMap(BookSortMap $sortMap): array
    {
        $modelMap = [];
        $ids = [
            'chapter' => [],
            'page' => [],
            'book' => [],
        ];

        foreach ($sortMap->all() as $sortMapItem) {
            $ids[$sortMapItem->type][] = $sortMapItem->id;
            $ids['book'][] = $sortMapItem->parentBookId;
            if ($sortMapItem->parentChapterId) {
                $ids['chapter'][] = $sortMapItem->parentChapterId;
            }
        }

        $pages = Page::visible()->whereIn('id', array_unique($ids['page']))->get(Page::$listAttributes);
        /** @var Page $page */
        foreach ($pages as $page) {
            $modelMap['page:' . $page->id] = $page;
            $ids['book'][] = $page->book_id;
            if ($page->chapter_id) {
                $ids['chapter'][] = $page->chapter_id;
            }
        }

        $chapters = Chapter::visible()->whereIn('id', array_unique($ids['chapter']))->get();
        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            $modelMap['chapter:' . $chapter->id] = $chapter;
            $ids['book'][] = $chapter->book_id;
        }

        $books = Book::visible()->whereIn('id', array_unique($ids['book']))->get();
        /** @var Book $book */
        foreach ($books as $book) {
            $modelMap['book:' . $book->id] = $book;
        }

        return $modelMap;
    }
}

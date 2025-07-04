<?php

namespace BookStack\Sorting;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Record;
use BookStack\Entities\Models\RecordChapter;
use BookStack\Entities\Models\RecordChild;
use BookStack\Entities\Models\RecordPage;
use BookStack\Entities\Queries\EntityQueries;
use Illuminate\Support\Collection;

class RecordSorter
{
     public function __construct(
        protected EntityQueries $queries,
    ) {
    }

    public function runBookAutoSortForAllWithSet(SortRule $set): void
    {
        $set->record()->chunk(50, function ($books) {
            foreach ($books as $book) {
                $this->runBookAutoSort($book);
            }
        });
    }

    /**
     * Runs the auto-sort for a book if the book has a sort set applied to it.
     * This does not consider permissions since the sort operations are centrally
     * managed by admins so considered permitted if existing and assigned.
     */
    public function runBookAutoSort(Record $book): void
    {
        $set = $book->sortRule;
        if (!$set) {
            return;
        }

        $sortFunctions = array_map(function (SortRuleOperation $op) {
            return $op->getSortFunction();
        }, $set->getOperations());

        $chapters = $book->chapters()
            ->with('pages:id,name,priority,created_at,updated_at,chapter_id')
            ->get(['id', 'name', 'priority', 'created_at', 'updated_at']);

        /** @var (RecordChapter|Record)[] $topItems */
        $topItems = [
            ...$book->directPages()->get(['id', 'name', 'priority', 'created_at', 'updated_at']),
            ...$chapters,
        ];

        foreach ($sortFunctions as $sortFunction) {
            usort($topItems, $sortFunction);
        }

        foreach ($topItems as $index => $topItem) {
            $topItem->priority = $index + 1;
            $topItem::withoutTimestamps(fn () => $topItem->save());
        }

        foreach ($chapters as $chapter) {
            $pages = $chapter->pages->all();
            foreach ($sortFunctions as $sortFunction) {
                usort($pages, $sortFunction);
            }

            foreach ($pages as $index => $page) {
                $page->priority = $index + 1;
                $page::withoutTimestamps(fn () => $page->save());
            }
        }
    }


    /**
     * Sort the books content using the given sort map.
     * Returns a list of books that were involved in the operation.
     *
     * @returns Book[]
     */
    public function sortUsingMap(RecordSortMap $sortMap): array
    {
        // Load models into map
        $modelMap = $this->loadModelsFromSortMap($sortMap);

        // Sort our changes from our map to be chapters first
        // Since they need to be process to ensure book alignment for child page changes.
        $sortMapItems = $sortMap->all();
        usort($sortMapItems, function (BookSortMapItem $itemA, BookSortMapItem $itemB) {
            $aScore = $itemA->type === 'page' ? 2 : 1;
            $bScore = $itemB->type === 'page' ? 2 : 1;

            return $aScore - $bScore;
        });

        // Perform the sort
        foreach ($sortMapItems as $item) {
            $this->applySortUpdates($item, $modelMap);
        }

        /** @var Record[] $booksInvolved */
        $booksInvolved = array_values(array_filter($modelMap, function (string $key) {
            return str_starts_with($key, 'book:');
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
    protected function applySortUpdates(RecordSortMapItem $sortMapItem, array $modelMap): void
    {
        /** @var RecordChild $model */
        $model = $modelMap[$sortMapItem->type . ':' . $sortMapItem->id] ?? null;
        if (!$model) {
            return;
        }

        $priorityChanged = $model->priority !== $sortMapItem->sort;
        $bookChanged = $model->book_id !== $sortMapItem->parentBookId;
        $chapterChanged = ($model instanceof RecordPage) && $model->chapter_id !== $sortMapItem->parentChapterId;

        // Stop if there's no change
        if (!$priorityChanged && !$bookChanged && !$chapterChanged) {
            return;
        }

        $currentParentKey = 'record:' . $model->book_id;
        if ($model instanceof RecordPage && $model->chapter_id) {
            $currentParentKey = 'chapter:' . $model->chapter_id;
        }

        $currentParent = $modelMap[$currentParentKey] ?? null;
        /** @var Record $newBook */
        $newBook = $modelMap['record:' . $sortMapItem->parentBookId] ?? null;
        /** @var ?RecordChapter $newChapter */
        $newChapter = $sortMapItem->parentChapterId ? ($modelMap['chapter:' . $sortMapItem->parentChapterId] ?? null) : null;

        if (!$this->isSortChangePermissible($sortMapItem, $model, $currentParent, $newBook, $newChapter)) {
            return;
        }

        // Action the required changes
        if ($bookChanged) {
            $model->changeBook($newBook->id);
        }

        if ($model instanceof RecordPage && $chapterChanged) {
            $model->chapter_id = $newChapter->id ?? 0;
        }

        if ($priorityChanged) {
            $model->priority = $sortMapItem->sort;
        }

        if ($chapterChanged || $priorityChanged) {
            $model::withoutTimestamps(fn () => $model->save());
        }
    }

    /**
     * Check if the current user has permissions to apply the given sorting change.
     * Is quite complex since items can gain a different parent change. Acts as a:
     * - Update of old parent element (Change of content/order).
     * - Update of sorted/moved element.
     * - Deletion of element (Relative to parent upon move).
     * - Creation of element within parent (Upon move to new parent).
     */
    protected function isSortChangePermissible(RecordSortMapItem $sortMapItem, RecordChild $model, ?Entity $currentParent, ?Entity $newBook, ?Entity $newChapter): bool
    {
        // Stop if we can't see the current parent or new book.
        if (!$currentParent || !$newBook) {
            return false;
        }

        $hasNewParent = $newBook->id !== $model->book_id || ($model instanceof RecordPage && $model->chapter_id !== ($sortMapItem->parentChapterId ?? 0));
        if ($model instanceof RecordChapter) {
            $hasPermission = userCan('book-update', $currentParent)
                && userCan('book-update', $newBook)
                && userCan('chapter-update', $model)
                && (!$hasNewParent || userCan('chapter-create', $newBook))
                && (!$hasNewParent || userCan('chapter-delete', $model));

            if (!$hasPermission) {
                return false;
            }
        }

        if ($model instanceof RecordPage) {
            $parentPermission = ($currentParent instanceof RecordChapter) ? 'chapter-update' : 'book-update';
            $hasCurrentParentPermission = userCan($parentPermission, $currentParent);

            // This needs to check if there was an intended chapter location in the original sort map
            // rather than inferring from the $newChapter since that variable may be null
            // due to other reasons (Visibility).
            $newParent = $sortMapItem->parentChapterId ? $newChapter : $newBook;
            if (!$newParent) {
                return false;
            }

            $hasPageEditPermission = userCan('page-update', $model);
            $newParentInRightLocation = ($newParent instanceof Record || ($newParent instanceof RecordChapter && $newParent->record_id === $newBook->id));
            $newParentPermission = ($newParent instanceof RecordChapter) ? 'chapter-update' : 'record-update';
            $hasNewParentPermission = userCan($newParentPermission, $newParent);

            $hasDeletePermissionIfMoving = (!$hasNewParent || userCan('page-delete', $model));
            $hasCreatePermissionIfMoving = (!$hasNewParent || userCan('page-create', $newParent));

            $hasPermission = $hasCurrentParentPermission
                && $newParentInRightLocation
                && $hasNewParentPermission
                && $hasPageEditPermission
                && $hasDeletePermissionIfMoving
                && $hasCreatePermissionIfMoving;

            if (!$hasPermission) {
                return false;
            }
        }

        return true;
    }

    /**
     * Load models from the database into the given sort map.
     *
     * @return array<string, Entity>
     */
    protected function loadModelsFromSortMap(RecordSortMap $sortMap): array
    {
        $modelMap = [];
        $ids = [
            'chapter' => [],
            'page'    => [],
            'book'    => [],
        ];

        foreach ($sortMap->all() as $sortMapItem) {
            $ids[$sortMapItem->type][] = $sortMapItem->id;
            $ids['record'][] = $sortMapItem->parentBookId;
            if ($sortMapItem->parentChapterId) {
                $ids['recordChapter'][] = $sortMapItem->parentChapterId;
            }
        }

        $pages = $this->queries->pages->visibleForList()->whereIn('id', array_unique($ids['recordPage']))->get();
        /** @var RecordPage $page */
        foreach ($pages as $page) {
            $modelMap['page:' . $page->id] = $page;
            $ids['book'][] = $page->book_id;
            if ($page->chapter_id) {
                $ids['chapter'][] = $page->chapter_id;
            }
        }

        $chapters = $this->queries->chapters->visibleForList()->whereIn('id', array_unique($ids['chapter']))->get();
        /** @var RecordChapter $chapter */
        foreach ($chapters as $chapter) {
            $modelMap['chapter:' . $chapter->id] = $chapter;
            $ids['book'][] = $chapter->book_id;
        }

        $books = $this->queries->books->visibleForList()->whereIn('id', array_unique($ids['book']))->get();
        /** @var Record $book */
        foreach ($books as $book) {
            $modelMap['record:' . $book->id] = $book;
        }

        return $modelMap;
    }
}

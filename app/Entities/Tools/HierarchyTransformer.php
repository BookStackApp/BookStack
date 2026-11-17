<?php

namespace BookStack\Entities\Tools;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Facades\Activity;
use Illuminate\Database\Eloquent\Collection;

class HierarchyTransformer
{
    public function __construct(
        protected BookRepo $bookRepo,
        protected BookshelfRepo $shelfRepo,
        protected Cloner $cloner,
        protected TrashCan $trashCan,
        protected ParentChanger $parentChanger,
    ) {
    }

    /**
     * Transform a chapter into a book.
     * Does not check permissions, check before calling.
     */
    public function transformChapterToBook(Chapter $chapter): Book
    {
        $inputData = $this->cloner->entityToInputData($chapter);
        $book = $this->bookRepo->create($inputData);
        $this->cloner->copyEntityPermissions($chapter, $book);

        if ($chapter->deleted_at) {
            $this->copyChapterDeletionToBook($chapter, $book);
        }

        /** @var Collection<int, Page> $pages */
        $pages = $chapter->pages()->withTrashed()->get();
        foreach ($pages as $page) {
            $page->chapter_id = null;
            $page->save();
            $this->parentChanger->changeBook($page, $book->id);
        }

        $this->trashCan->destroyEntity($chapter);

        Activity::add(ActivityType::BOOK_CREATE_FROM_CHAPTER, $book);
        $book->rebuildPermissions();

        return $book;
    }

    /**
     * Transform a book into a shelf.
     * Does not check permissions, check before calling.
     */
    public function transformBookToShelf(Book $book): Bookshelf
    {
        $inputData = $this->cloner->entityToInputData($book);
        $shelf = $this->shelfRepo->create($inputData, []);
        $this->cloner->copyEntityPermissions($book, $shelf);

        $shelfBookSyncData = [];

        /** @var Collection<int, Chapter> $chapters */
        $chapters = $book->chapters()->withTrashed()->get();
        foreach ($chapters as $index => $chapter) {
            $newBook = $this->transformChapterToBook($chapter);
            $shelfBookSyncData[$newBook->id] = ['order' => $index];
            if (!$newBook->hasPermissions()) {
                $this->cloner->copyEntityPermissions($shelf, $newBook);
            }
        }

        if ($book->directPages()->withTrashed()->count() > 0) {
            $book->name .= ' ' . trans('entities.pages');
            $shelfBookSyncData[$book->id] = ['order' => count($shelfBookSyncData) + 1];
            $book->save();
        } else {
            $this->trashCan->destroyEntity($book);
        }

        $shelf->books()->sync($shelfBookSyncData);

        Activity::add(ActivityType::BOOKSHELF_CREATE_FROM_BOOK, $shelf);

        return $shelf;
    }

    public function copyChapterDeletionToBook(Chapter $chapter, Book $book): void
    {
        $book->deleted_at = $chapter->deleted_at;
        $book->save();

        $deletions = $chapter->deletions()->get();
        /** @var Deletion $deletion */
        foreach ($deletions as $deletion) {
            $deletion->deletable_id = $book->id;
            $deletion->deletable_type = 'book';
            $deletion->save();
        }
    }
}

<?php

namespace BookStack\Entities\Tools;

use BookStack\Actions\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Facades\Activity;

class HierarchyTransformer
{
    protected BookRepo $bookRepo;
    protected BookshelfRepo $shelfRepo;
    protected Cloner $cloner;
    protected TrashCan $trashCan;

    public function __construct(BookRepo $bookRepo, BookshelfRepo $shelfRepo, Cloner $cloner, TrashCan $trashCan)
    {
        $this->bookRepo = $bookRepo;
        $this->shelfRepo = $shelfRepo;
        $this->cloner = $cloner;
        $this->trashCan = $trashCan;
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

        /** @var Page $page */
        foreach ($chapter->pages as $page) {
            $page->chapter_id = 0;
            $page->changeBook($book->id);
        }

        $this->trashCan->destroyEntity($chapter);

        Activity::add(ActivityType::BOOK_CREATE_FROM_CHAPTER, $book);

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

        /** @var Chapter $chapter */
        foreach ($book->chapters as $index => $chapter) {
            $newBook = $this->transformChapterToBook($chapter);
            $shelfBookSyncData[$newBook->id] = ['order' => $index];
            if (!$newBook->restricted) {
                $this->cloner->copyEntityPermissions($shelf, $newBook);
            }
        }

        if ($book->directPages->count() > 0) {
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
}

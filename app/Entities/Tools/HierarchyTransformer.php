<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;

class HierarchyTransformer
{
    protected BookRepo $bookRepo;
    protected BookshelfRepo $shelfRepo;
    protected Cloner $cloner;
    protected TrashCan $trashCan;

    // TODO - Test setting book cover image from API
    //   Ensure we can update without resetting image accidentally
    //   Ensure api docs correct.
    // TODO - As above but for shelves.

    public function transformChapterToBook(Chapter $chapter): Book
    {
        // TODO - Check permissions before call
        //   Permissions: edit-chapter, delete-chapter, create-book
        $inputData = $this->cloner->entityToInputData($chapter);
        $book = $this->bookRepo->create($inputData);

        // TODO - Copy permissions

        /** @var Page $page */
        foreach ($chapter->pages as $page) {
            $page->chapter_id = 0;
            $page->changeBook($book->id);
        }

        $this->trashCan->destroyEntity($chapter);

        // TODO - Log activity for change
        return $book;
    }

    public function transformBookToShelf(Book $book): Bookshelf
    {
        // TODO - Check permissions before call
        //   Permissions: edit-book, delete-book, create-shelf
        $inputData = $this->cloner->entityToInputData($book);
        $shelf = $this->shelfRepo->create($inputData, []);

        // TODO - Copy permissions?

        $shelfBookSyncData = [];

        /** @var Chapter $chapter */
        foreach ($book->chapters as $index => $chapter) {
            $newBook = $this->transformChapterToBook($chapter);
            $shelfBookSyncData[$newBook->id] = ['order' => $index];
        }

        $shelf->books()->sync($shelfBookSyncData);

        if ($book->directPages->count() > 0) {
            $book->name .= ' ' . trans('entities.pages');
        } else {
            $this->trashCan->destroyEntity($book);
        }

        // TODO - Log activity for change
        return $shelf;
    }
}
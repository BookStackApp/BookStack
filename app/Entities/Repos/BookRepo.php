<?php


namespace BookStack\Entities\Repos;

use BookStack\Entities\Book;
use BookStack\Exceptions\NotifyException;

class BookRepo extends EntityRepo
{

    /**
     * Fetch a book by its slug.
     * @param string $slug
     * @return Book
     */
    public function getBySlug(string $slug): Book
    {
        return Book::visible()->where('slug', $slug)->firstOrFail();
    }

    /**
     * Destroy the provided book and all its child entities.
     * @param Book $book
     * @throws NotifyException
     * @throws \Throwable
     */
    public function destroyBook(Book $book)
    {
        foreach ($book->pages as $page) {
            $this->destroyPage($page);
        }

        foreach ($book->chapters as $chapter) {
            $this->destroyChapter($chapter);
        }

        $this->destroyEntityCommonRelations($book);
        $book->delete();
    }

}
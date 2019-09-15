<?php


namespace BookStack\Entities\Repos;


use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\NotifyException;

class BookRepo extends EntityRepo
{

    /**
     * Fetch a book by its slug.
     * @param string $slug
     * @return Book
     * @throws NotFoundException
     */
    public function getBySlug(string $slug): Book
    {
        /** @var Book $book */
        $book = $this->getEntityBySlug('book', $slug);
        return $book;
    }

    /**
     * Append a Book to a BookShelf.
     * @param Bookshelf $shelf
     * @param Book $book
     */
    public function appendBookToShelf(Bookshelf $shelf, Book $book)
    {
        if ($shelf->contains($book)) {
            return;
        }

        $maxOrder = $shelf->books()->max('order');
        $shelf->books()->attach($book->id, ['order' => $maxOrder + 1]);
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
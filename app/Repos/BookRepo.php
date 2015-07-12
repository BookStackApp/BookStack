<?php namespace Oxbow\Repos;

use Oxbow\Book;

class BookRepo
{

    protected $book;

    /**
     * BookRepo constructor.
     * @param $book
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function getById($id)
    {
        return $this->book->findOrFail($id);
    }

    public function getAll()
    {
        return $this->book->all();
    }

    public function getBySlug($slug)
    {
        return $this->book->where('slug', '=', $slug)->first();
    }

    public function newFromInput($input)
    {
        return $this->book->fill($input);
    }

    public function countBySlug($slug)
    {
        return $this->book->where('slug', '=', $slug)->count();
    }

    public function destroyById($id)
    {
        $book = $this->getById($id);
        $book->delete();
    }

}
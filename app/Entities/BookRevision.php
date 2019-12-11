<?php

namespace BookStack\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookRevision extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Get the book
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }

    /**
     * Get the pageRevisions that is part of this book revision
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pageRevisions()
    {
        return $this->belongsToMany(PageRevision::class, 'book_revision_has_page_revision', 'book_revision_id', 'page_revision_id');
    }

    /**
     * Creates a BookRevision based on a book_id
     * @param $book_id
     * @param $name
     * @return BookRevision|null
     * @throws ModelNotFoundException
     */
    public static function createBookRevision($book_id, $name)
    {
        $book = Book::findOrFail($book_id);

        $bookRevision = null;
        // Get all page_id`s for the current book
        $pages = Page::where('book_id', $book->id)->pluck('id');
        $lastPageRevisionsForBook = PageRevision::whereIn('page_id', $pages)->lastPerGroup()->get()->pluck('id');
        if ($lastPageRevisionsForBook->count() > 0) {
            // Creating bookRevision
            $bookRevision = new BookRevision();
            $bookRevision->name = $name;
            $bookRevision->book()->associate($book);
            $bookRevision->save();
            $bookRevision->pageRevisions()->sync($lastPageRevisionsForBook);
        }
        return $bookRevision;
    }
}

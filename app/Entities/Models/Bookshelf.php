<?php

namespace BookStack\Entities\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bookshelf extends Entity
{
    use HasFactory;

    public float $searchFactor = 1.2;

    protected $hidden = ['image_id', 'deleted_at', 'description_html'];

    /**
     * Get the books in this shelf.
     * Should not be used directly since it does not take into account permissions.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'bookshelves_books', 'bookshelf_id', 'book_id')
            ->withPivot('order')
            ->orderBy('order', 'asc');
    }

    /**
     * Related books that are visible to the current user.
     */
    public function visibleBooks(): BelongsToMany
    {
        return $this->books()->scopes('visible');
    }

    /**
     * Get the url for this bookshelf.
     */
    public function getUrl(string $path = ''): string
    {
        return url('/shelves/' . implode('/', [urlencode($this->slug), trim($path, '/')]));
    }

    /**
     * Returns shelf cover image, if cover not exists return default cover image.
     */
    public function getBookCover(int $width = 440, int $height = 250): string
    {
        // TODO - Make generic, focused on books right now, Perhaps set-up a better image
        $default = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
        if (!$this->containerData->image_id || !$this->containerData->cover) {
            return $default;
        }

        try {
            return $this->containerData->cover->getThumb($width, $height, false) ?? $default;
        } catch (Exception $err) {
            return $default;
        }
    }

    // TODO - Still handle cover as relation through containerData (since it's used in code)
    // TODO - Remove above since we can access that via containerData

    /**
     * Check if this shelf contains the given book.
     */
    public function contains(Book $book): bool
    {
        return $this->books()->where('id', '=', $book->id)->count() > 0;
    }

    /**
     * Add a book to the end of this shelf.
     */
    public function appendBook(Book $book): void
    {
        if ($this->contains($book)) {
            return;
        }

        $maxOrder = $this->books()->max('order');
        $this->books()->attach($book->id, ['order' => $maxOrder + 1]);
    }
}

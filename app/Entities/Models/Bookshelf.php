<?php

namespace BookStack\Entities\Models;

use BookStack\Uploads\Image;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bookshelf extends Entity implements HasCoverImage
{
    use HasFactory;

    protected $table = 'bookshelves';

    public $searchFactor = 1.2;

    protected $fillable = ['name', 'description', 'image_id'];

    protected $hidden = ['image_id', 'deleted_at'];

    /**
     * Get the books in this shelf.
     * Should not be used directly since does not take into account permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function books()
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
        if (!$this->image_id || !$this->cover) {
            return $default;
        }

        try {
            return $this->cover->getThumb($width, $height, false) ?? $default;
        } catch (Exception $err) {
            return $default;
        }
    }

    /**
     * Get the cover image of the shelf.
     */
    public function cover(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    /**
     * Get the type of the image model that is used when storing a cover image.
     */
    public function coverImageTypeKey(): string
    {
        return 'cover_bookshelf';
    }

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
    public function appendBook(Book $book)
    {
        if ($this->contains($book)) {
            return;
        }

        $maxOrder = $this->books()->max('order');
        $this->books()->attach($book->id, ['order' => $maxOrder + 1]);
    }

    /**
     * Get a visible shelf by its slug.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function getBySlug(string $slug): self
    {
        return static::visible()->where('slug', '=', $slug)->firstOrFail();
    }
}

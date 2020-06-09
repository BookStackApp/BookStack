<?php namespace BookStack\Entities;

use BookStack\Uploads\Image;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bookshelf extends Entity implements HasCoverImage
{
    protected $table = 'bookshelves';

    public $searchFactor = 3;

    protected $fillable = ['name', 'description', 'image_id'];

    protected $hidden = ['restricted', 'image_id'];

    /**
     * Get the books in this shelf.
     * Should not be used directly since does not take into account permissions.
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
        return $this->books()->visible();
    }

    /**
     * Get the url for this bookshelf.
     * @param string|bool $path
     * @return string
     */
    public function getUrl($path = false)
    {
        if ($path !== false) {
            return url('/shelves/' . urlencode($this->slug) . '/' . trim($path, '/'));
        }
        return url('/shelves/' . urlencode($this->slug));
    }

    /**
     * Returns BookShelf cover image, if cover does not exists return default cover image.
     * @param int $width - Width of the image
     * @param int $height - Height of the image
     * @return string
     */
    public function getBookCover($width = 440, $height = 250)
    {
        // TODO - Make generic, focused on books right now, Perhaps set-up a better image
        $default = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
        if (!$this->image_id) {
            return $default;
        }

        try {
            $cover = $this->cover ? url($this->cover->getThumb($width, $height, false)) : $default;
        } catch (\Exception $err) {
            $cover = $default;
        }
        return $cover;
    }

    /**
     * Get the cover image of the shelf
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
        return 'cover_shelf';
    }

    /**
     * Get an excerpt of this book's description to the specified length or less.
     * @param int $length
     * @return string
     */
    public function getExcerpt(int $length = 100)
    {
        $description = $this->description;
        return mb_strlen($description) > $length ? mb_substr($description, 0, $length-3) . '...' : $description;
    }

    /**
     * Check if this shelf contains the given book.
     * @param Book $book
     * @return bool
     */
    public function contains(Book $book): bool
    {
        return $this->books()->where('id', '=', $book->id)->count() > 0;
    }

    /**
     * Add a book to the end of this shelf.
     * @param Book $book
     */
    public function appendBook(Book $book)
    {
        if ($this->contains($book)) {
            return;
        }

        $maxOrder = $this->books()->max('order');
        $this->books()->attach($book->id, ['order' => $maxOrder + 1]);
    }
}

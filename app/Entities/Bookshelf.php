<?php namespace BookStack\Entities;

use BookStack\Uploads\Image;

class Bookshelf extends Entity
{
    protected $table = 'bookshelves';

    public $searchFactor = 3;

    protected $fillable = ['name', 'description', 'image_id'];

    /**
     * Get the morph class for this model.
     * @return string
     */
    public function getMorphClass()
    {
        return 'BookStack\\Bookshelf';
    }

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
     * Get the url for this bookshelf.
     * @param string|bool $path
     * @return string
     */
    public function getUrl($path = false)
    {
        if ($path !== false) {
            return baseUrl('/shelves/' . urlencode($this->slug) . '/' . trim($path, '/'));
        }
        return baseUrl('/shelves/' . urlencode($this->slug));
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
            $cover = $this->cover ? baseUrl($this->cover->getThumb($width, $height, false)) : $default;
        } catch (\Exception $err) {
            $cover = $default;
        }
        return $cover;
    }

    /**
     * Get the cover image of the shelf
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cover()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    /**
     * Get an excerpt of this book's description to the specified length or less.
     * @param int $length
     * @return string
     */
    public function getExcerpt(int $length = 100)
    {
        $description = $this->description;
        return strlen($description) > $length ? substr($description, 0, $length-3) . '...' : $description;
    }

    /**
     * Return a generalised, common raw query that can be 'unioned' across entities.
     * @return string
     */
    public function entityRawQuery()
    {
        return "'BookStack\\\\BookShelf' as entity_type, id, id as entity_id, slug, name, {$this->textField} as text,'' as html, '0' as book_id, '0' as priority, '0' as chapter_id, '0' as draft, created_by, updated_by, updated_at, created_at";
    }

    /**
     * Check if this shelf contains the given book.
     * @param Book $book
     * @return bool
     */
    public function contains(Book $book)
    {
        return $this->books()->where('id', '=', $book->id)->count() > 0;
    }
}

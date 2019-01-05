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
        return $this->belongsToMany(Book::class, 'bookshelves_books', 'bookshelf_id', 'book_id')->orderBy('order', 'asc');
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
        $default = baseUrl('/book_default_cover.png');
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
     * Get the cover image of the book
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
    public function getExcerpt($length = 100)
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
}

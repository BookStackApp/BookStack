<?php

namespace BookStack\Entities\Models;

use BookStack\Uploads\Image;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Book.
 *
 * @property string                                   $description
 * @property int                                      $image_id
 * @property Image|null                               $cover
 * @property \Illuminate\Database\Eloquent\Collection $chapters
 * @property \Illuminate\Database\Eloquent\Collection $pages
 * @property \Illuminate\Database\Eloquent\Collection $directPages
 */
class Book extends Entity implements HasCoverImage
{
    use HasFactory;

    public $searchFactor = 1.5;

    protected $fillable = ['name', 'description'];
    protected $hidden = ['restricted', 'pivot', 'image_id', 'deleted_at'];

    /**
     * Get the url for this book.
     */
    public function getUrl(string $path = ''): string
    {
        return url('/books/' . implode('/', [urlencode($this->slug), trim($path, '/')]));
    }

    /**
     * Returns book cover image, if book cover not exists return default cover image.
     *
     * @param int $width  - Width of the image
     * @param int $height - Height of the image
     *
     * @return string
     */
    public function getBookCover($width = 440, $height = 250)
    {
        $default = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
        if (!$this->image_id) {
            return $default;
        }

        try {
            $cover = $this->cover ? url($this->cover->getThumb($width, $height, false)) : $default;
        } catch (Exception $err) {
            $cover = $default;
        }

        return $cover;
    }

    /**
     * Get the cover image of the book.
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
        return 'cover_book';
    }

    /**
     * Get all pages within this book.
     *
     * @return HasMany
     */
    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get the direct child pages of this book.
     *
     * @return HasMany
     */
    public function directPages()
    {
        return $this->pages()->where('chapter_id', '=', '0');
    }

    /**
     * Get all chapters within this book.
     *
     * @return HasMany
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * Get the shelves this book is contained within.
     *
     * @return BelongsToMany
     */
    public function shelves()
    {
        return $this->belongsToMany(Bookshelf::class, 'bookshelves_books', 'book_id', 'bookshelf_id');
    }

    /**
     * Get the direct child items within this book.
     *
     * @return Collection
     */
    public function getDirectChildren(): Collection
    {
        $pages = $this->directPages()->visible()->get();
        $chapters = $this->chapters()->visible()->get();

        return $pages->concat($chapters)->sortBy('priority')->sortByDesc('draft');
    }
}

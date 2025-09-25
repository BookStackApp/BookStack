<?php

namespace BookStack\Entities\Models;

use BookStack\Sorting\SortRule;
use BookStack\Uploads\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * Class Book.
 *
 * @property string                                   $description
 * @property int                                      $image_id
 * @property ?int                                     $default_template_id
 * @property ?int                                     $sort_rule_id
 * @property Image|null                               $cover
 * @property \Illuminate\Database\Eloquent\Collection $chapters
 * @property \Illuminate\Database\Eloquent\Collection $pages
 * @property \Illuminate\Database\Eloquent\Collection $directPages
 * @property \Illuminate\Database\Eloquent\Collection $shelves
 * @property ?Page                                    $defaultTemplate
 * @property ?SortRule                                 $sortRule
 */
class Book extends Entity
{
    use HasFactory;
    use ContainerTrait;

    public float $searchFactor = 1.2;

    protected $hidden = ['pivot', 'image_id', 'deleted_at', 'description_html'];

    /**
     * Get the url for this book.
     */
    public function getUrl(string $path = ''): string
    {
        return url('/books/' . implode('/', [urlencode($this->slug), trim($path, '/')]));
    }

    /**
     * Returns a book cover image URL or a default URL if no cover image set.
     */
    public function getCover(int $width = 440, int $height = 250): string
    {
        return $this->contents()->getCoverUrl($width, $height);
    }

    /**
     * Get all pages within this book.
     * @return HasMany<Page, $this>
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get the direct child pages of this book.
     */
    public function directPages(): HasMany
    {
        return $this->pages()->where('chapter_id', '=', '0');
    }

    /**
     * Get all chapters within this book.
     * @return HasMany<Chapter, $this>
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * Get the shelves this book is contained within.
     */
    public function shelves(): BelongsToMany
    {
        return $this->belongsToMany(Bookshelf::class, 'bookshelves_books', 'book_id', 'bookshelf_id');
    }

    /**
     * Get the direct child items within this book.
     */
    public function getDirectVisibleChildren(): Collection
    {
        $pages = $this->directPages()->scopes('visible')->get();
        $chapters = $this->chapters()->scopes('visible')->get();

        return $pages->concat($chapters)->sortBy('priority')->sortByDesc('draft');
    }
}

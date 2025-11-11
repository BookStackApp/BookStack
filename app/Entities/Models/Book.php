<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityCover;
use BookStack\Entities\Tools\EntityDefaultTemplate;
use BookStack\Sorting\SortRule;
use BookStack\Uploads\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Book.
 *
 * @property string                                   $description
 * @property string                                   $description_html
 * @property int                                      $image_id
 * @property ?int                                     $default_template_id
 * @property ?int                                     $sort_rule_id
 * @property \Illuminate\Database\Eloquent\Collection $chapters
 * @property \Illuminate\Database\Eloquent\Collection $pages
 * @property \Illuminate\Database\Eloquent\Collection $directPages
 * @property \Illuminate\Database\Eloquent\Collection $shelves
 * @property ?SortRule                                $sortRule
 */
class Book extends Entity implements HasDescriptionInterface, HasCoverInterface, HasDefaultTemplateInterface
{
    use HasFactory;
    use ContainerTrait;

    public float $searchFactor = 1.2;

    protected $hidden = ['pivot', 'deleted_at', 'description_html', 'entity_id', 'entity_type', 'chapter_id', 'book_id', 'priority'];
    protected $fillable = ['name'];

    /**
     * Get the url for this book.
     */
    public function getUrl(string $path = ''): string
    {
        return url('/books/' . implode('/', [urlencode($this->slug), trim($path, '/')]));
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
        return $this->pages()->whereNull('chapter_id');
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

    public function defaultTemplate(): EntityDefaultTemplate
    {
        return new EntityDefaultTemplate($this);
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function coverInfo(): EntityCover
    {
        return new EntityCover($this);
    }

    /**
     * Get the sort rule assigned to this container, if existing.
     */
    public function sortRule(): BelongsTo
    {
        return $this->belongsTo(SortRule::class);
    }
}

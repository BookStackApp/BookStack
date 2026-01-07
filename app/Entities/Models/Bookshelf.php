<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityCover;
use BookStack\Uploads\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $description
 * @property string $description_html
 */
class Bookshelf extends Entity implements HasDescriptionInterface, HasCoverInterface
{
    use HasFactory;
    use ContainerTrait;

    public float $searchFactor = 1.2;

    protected $hidden = ['image_id', 'deleted_at', 'description_html', 'priority', 'default_template_id', 'sort_rule_id', 'entity_id', 'entity_type', 'chapter_id', 'book_id'];
    protected $fillable = ['name'];

    /**
     * Get the books in this shelf.
     * Should not be used directly since it does not take into account permissions.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'bookshelves_books', 'bookshelf_id', 'book_id')
            ->select(['entities.*', 'entity_container_data.*'])
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

    public function coverInfo(): EntityCover
    {
        return new EntityCover($this);
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}

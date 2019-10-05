<?php namespace BookStack\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BookChild
 * @property int $book_id
 * @property int $priority
 * @property Book $book
 * @method Builder whereSlugs(string $bookSlug, string $childSlug)
 */
class BookChild extends Entity
{

    /**
     * Scope a query to find items where the the child has the given childSlug
     * where its parent has the bookSlug.
     */
    public function scopeWhereSlugs(Builder $query, string $bookSlug, string $childSlug)
    {
        return $query->with('book')
            ->whereHas('book', function (Builder $query) use ($bookSlug) {
                $query->where('slug', '=', $bookSlug);
            })
            ->where('slug', '=', $childSlug);
    }

    /**
     * Get the book this page sits in.
     * @return BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Change the book that this entity belongs to.
     */
    public function changeBook(int $newBookId): Entity
    {
        $this->book_id = $newBookId;
        $this->refreshSlug();
        $this->save();
        $this->refresh();

        // Update related activity
        $this->activity()->update(['book_id' => $newBookId]);

        // Update all child pages if a chapter
        if ($this instanceof Chapter) {
            foreach ($this->pages as $page) {
                $page->changeBook($newBookId);
            }
        }

        return $this;
    }
}

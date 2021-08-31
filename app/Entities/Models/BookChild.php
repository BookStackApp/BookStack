<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BookChild.
 *
 * @property int    $book_id
 * @property int    $priority
 * @property string $book_slug
 * @property Book   $book
 *
 * @method Builder whereSlugs(string $bookSlug, string $childSlug)
 */
abstract class BookChild extends Entity
{
    protected static function boot()
    {
        parent::boot();

        // Load book slugs onto these models by default during query-time
        static::addGlobalScope('book_slug', function (Builder $builder) {
            $builder->addSelect(['book_slug' => function ($builder) {
                $builder->select('slug')
                    ->from('books')
                    ->whereColumn('books.id', '=', 'book_id');
            }]);
        });
    }

    /**
     * Scope a query to find items where the child has the given childSlug
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
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
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

        // Update all child pages if a chapter
        if ($this instanceof Chapter) {
            foreach ($this->pages()->withTrashed()->get() as $page) {
                $page->changeBook($newBookId);
            }
        }

        return $this;
    }
}

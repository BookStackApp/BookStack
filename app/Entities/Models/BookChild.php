<?php

namespace BookStack\Entities\Models;

use BookStack\References\ReferenceUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BookChild.
 *
 * @property int    $book_id
 * @property int    $priority
 * @property string $book_slug
 * @property Book   $book
 */
abstract class BookChild extends Entity
{
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
        $oldUrl = $this->getUrl();
        $this->book_id = $newBookId;
        $this->refreshSlug();
        $this->save();
        $this->refresh();

        if ($oldUrl !== $this->getUrl()) {
            app()->make(ReferenceUpdater::class)->updateEntityReferences($this, $oldUrl);
        }

        // Update all child pages if a chapter
        if ($this instanceof Chapter) {
            foreach ($this->pages()->withTrashed()->get() as $page) {
                $page->changeBook($newBookId);
            }
        }

        return $this;
    }
}

<?php

namespace BookStack\Entities\Models;

use BookStack\References\ReferenceUpdater;
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
    public function changeBook(int $newBookId): self
    {
        $altered = $this->clone()->refresh();
        $oldUrl = $altered->getUrl();
        $altered->book_id = $newBookId;
        $altered->refreshSlug();
        $altered->save();

        if ($oldUrl !== $altered->getUrl()) {
            app()->make(ReferenceUpdater::class)->updateEntityReferences($altered, $oldUrl);
        }

        // Update all child pages if a chapter
        if ($altered instanceof Chapter) {
            foreach ($altered->pages()->withTrashed()->get() as $page) {
                $page->changeBook($newBookId);
            }
        }

        return $altered;
    }
}

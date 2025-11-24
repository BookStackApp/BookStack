<?php

namespace BookStack\Entities\Models;

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
     * @return BelongsTo<Book, $this>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
}

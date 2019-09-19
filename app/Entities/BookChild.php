<?php namespace BookStack\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BookChild
 * @property int $book_id
 */
class BookChild extends Entity
{

    /**
     * Get the book this page sits in.
     * @return BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

}
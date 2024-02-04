<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Book;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

class BookQueries
{
    public function start(): Builder
    {
        return Book::query();
    }

    public function findVisibleBySlug(string $slug): Book
    {
        /** @var ?Book $book */
        $book = $this->start()
            ->scopes('visible')
            ->where('slug', '=', $slug)
            ->first();

        if ($book === null) {
            throw new NotFoundException(trans('errors.book_not_found'));
        }

        return $book;
    }

    public function visibleForList(): Builder
    {
        return $this->start()->scopes('visible');
    }

    public function visibleForListWithCover(): Builder
    {
        return $this->visibleForList()->with('cover');
    }

    public function recentlyViewedForCurrentUser(): Builder
    {
        return $this->visibleForList()
            ->scopes('withLastView')
            ->having('last_viewed_at', '>', 0)
            ->orderBy('last_viewed_at', 'desc');
    }

    public function popularForList(): Builder
    {
        return $this->visibleForList()
            ->scopes('withViewCount')
            ->having('view_count', '>', 0)
            ->orderBy('view_count', 'desc');
    }
}

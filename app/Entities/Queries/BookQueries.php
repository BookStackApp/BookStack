<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Book;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

class BookQueries implements ProvidesEntityQueries
{
    protected static array $listAttributes = [
        'id', 'slug', 'name', 'description',
        'created_at', 'updated_at', 'image_id', 'owned_by',
    ];

    public function start(): Builder
    {
        return Book::query();
    }

    public function findVisibleById(int $id): ?Book
    {
        return $this->start()->scopes('visible')->find($id);
    }

    public function findVisibleByIdOrFail(int $id): Book
    {
        return $this->start()->scopes('visible')->findOrFail($id);
    }

    public function findVisibleBySlugOrFail(string $slug): Book
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
        return $this->start()->scopes('visible')
            ->select(static::$listAttributes);
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

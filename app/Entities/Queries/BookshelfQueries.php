<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Bookshelf;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

class BookshelfQueries implements ProvidesEntityQueries
{
    protected static array $listAttributes = [
        'id', 'slug', 'name', 'description',
        'created_at', 'updated_at', 'image_id', 'owned_by',
    ];

    public function start(): Builder
    {
        return Bookshelf::query();
    }

    public function findVisibleById(int $id): ?Bookshelf
    {
        return $this->start()->scopes('visible')->find($id);
    }

    public function findVisibleByIdOrFail(int $id): Bookshelf
    {
        $shelf = $this->findVisibleById($id);

        if (is_null($shelf)) {
            throw new NotFoundException(trans('errors.bookshelf_not_found'));
        }

        return $shelf;
    }

    public function findVisibleBySlugOrFail(string $slug): Bookshelf
    {
        /** @var ?Bookshelf $shelf */
        $shelf = $this->start()
            ->scopes('visible')
            ->where('slug', '=', $slug)
            ->first();

        if ($shelf === null) {
            throw new NotFoundException(trans('errors.bookshelf_not_found'));
        }

        return $shelf;
    }

    public function visibleForList(): Builder
    {
        return $this->start()->scopes('visible')->select(static::$listAttributes);
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

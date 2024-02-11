<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Chapter;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

class ChapterQueries implements ProvidesEntityQueries
{
    protected static array $listAttributes = [
        'id', 'slug', 'name', 'description', 'priority',
        'book_id', 'created_at', 'updated_at', 'owned_by',
    ];

    public function start(): Builder
    {
        return Chapter::query();
    }

    public function findVisibleById(int $id): ?Chapter
    {
        return $this->start()->scopes('visible')->find($id);
    }

    public function findVisibleByIdOrFail(int $id): Chapter
    {
        return $this->start()->scopes('visible')->findOrFail($id);
    }

    public function findVisibleBySlugsOrFail(string $bookSlug, string $chapterSlug): Chapter
    {
        /** @var ?Chapter $chapter */
        $chapter = $this->start()
            ->scopes('visible')
            ->with('book')
            ->whereHas('book', function (Builder $query) use ($bookSlug) {
                $query->where('slug', '=', $bookSlug);
            })
            ->where('slug', '=', $chapterSlug)
            ->first();

        if (is_null($chapter)) {
            throw new NotFoundException(trans('errors.chapter_not_found'));
        }

        return $chapter;
    }

    public function usingSlugs(string $bookSlug, string $chapterSlug): Builder
    {
        return $this->start()
            ->where('slug', '=', $chapterSlug)
            ->whereHas('book', function (Builder $query) use ($bookSlug) {
                $query->where('slug', '=', $bookSlug);
            });
    }

    public function visibleForList(): Builder
    {
        return $this->start()
            ->scopes('visible')
            ->select(array_merge(static::$listAttributes, ['book_slug' => function ($builder) {
                $builder->select('slug')
                    ->from('books')
                    ->whereColumn('books.id', '=', 'chapters.book_id');
            }]));
    }
}

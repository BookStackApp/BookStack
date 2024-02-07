<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Page;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

class PageQueries implements ProvidesEntityQueries
{
    public function start(): Builder
    {
        return Page::query();
    }

    public function findVisibleById(int $id): ?Page
    {
        return $this->start()->scopes('visible')->find($id);
    }

    public function findVisibleByIdOrFail(int $id): Page
    {
        $page = $this->findVisibleById($id);

        if (is_null($page)) {
            throw new NotFoundException(trans('errors.page_not_found'));
        }

        return $page;
    }

    public function findVisibleBySlugsOrFail(string $bookSlug, string $pageSlug): Page
    {
        /** @var ?Page $page */
        $page = $this->start()->with('book')
            ->scopes('visible')
            ->whereHas('book', function (Builder $query) use ($bookSlug) {
                $query->where('slug', '=', $bookSlug);
            })
            ->where('slug', '=', $pageSlug)
            ->first();

        if (is_null($page)) {
            throw new NotFoundException(trans('errors.chapter_not_found'));
        }

        return $page;
    }

    public function usingSlugs(string $bookSlug, string $pageSlug): Builder
    {
        return $this->start()
            ->where('slug', '=', $pageSlug)
            ->whereHas('book', function (Builder $query) use ($bookSlug) {
                $query->where('slug', '=', $bookSlug);
            });
    }

    public function visibleForList(): Builder
    {
        return $this->start()
            ->scopes('visible')
            ->select(array_merge(Page::$listAttributes, ['book_slug' => function ($builder) {
                $builder->select('slug')
                    ->from('books')
                    ->whereColumn('books.id', '=', 'pages.book_id');
            }]));
    }

    public function visibleWithContents(): Builder
    {
        return $this->start()
            ->scopes('visible')
            ->select(array_merge(Page::$contentAttributes, ['book_slug' => function ($builder) {
                $builder->select('slug')
                    ->from('books')
                    ->whereColumn('books.id', '=', 'pages.book_id');
            }]));
    }

    public function currentUserDraftsForList(): Builder
    {
        return $this->visibleForList()
            ->where('draft', '=', true)
            ->where('created_by', '=', user()->id);
    }

    public function visibleTemplates(): Builder
    {
        return $this->visibleForList()
            ->where('template', '=', true);
    }
}

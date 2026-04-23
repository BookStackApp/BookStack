<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Page;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

/**
 * @implements ProvidesEntityQueries<Page>
 */
class PageQueries implements ProvidesEntityQueries
{
    protected static array $contentAttributes = [
        'entities.name as name', 'entities.id as id', 'entities.slug as slug', 'entities.book_id as book_id',
        'entities.chapter_id as chapter_id', 'entity_page_data.draft as draft',
        'entity_page_data.template as template', 'entity_page_data.html as html', 'entity_page_data.markdown as markdown',
        'entity_page_data.text as text', 'entities.created_at as created_at', 'entities.updated_at as updated_at',
        'entities.priority as priority', 'entities.created_by as created_by', 'entities.updated_by as updated_by',
        'entities.owned_by as owned_by',
    ];
    protected static array $listAttributes = [
        'entities.name as name', 'entities.id as id', 'entities.slug as slug', 'entities.book_id as book_id',
        'entities.chapter_id as chapter_id', 'entity_page_data.draft as draft',
        'entity_page_data.template as template', 'entity_page_data.text as text', 'entities.created_at as created_at',
        'entities.updated_at as updated_at', 'entities.priority as priority', 'entities.owned_by as owned_by',
    ];

    /**
     * @return Builder<Page>
     */
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
            throw new NotFoundException(trans('errors.page_not_found'));
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

    /**
     * @return Builder<Page>
     */
    public function visibleForList(): Builder
    {
        return $this->start()
            ->scopes('visible')
            ->select(array_merge(
                $this->mergeBookSlugForSelect(static::$listAttributes),
                [
                    'entity_page_data.image_id',
                ]
            ));
    }

    /**
     * @return Builder<Page>
     */
    public function visibleForContent(): Builder
    {
        return $this->start()->scopes('visible');
    }

    public function visibleForChapterList(int $chapterId): Builder
    {
        return $this->visibleForListWithCover()
            ->where('entities.chapter_id', '=', $chapterId)
            ->orderBy('entity_page_data.draft', 'desc')
            ->orderBy('entities.priority', 'asc');
    }

    public function visibleWithContents(): Builder
    {
        return $this->start()
            ->scopes('visible')
            ->select(array_merge(
                $this->mergeBookSlugForSelect(static::$contentAttributes),
                [
                    'entity_page_data.image_id',
                ]
            ));
    }

    public function currentUserDraftsForList(): Builder
    {
        return $this->visibleForList()
            ->where('entity_page_data.draft', '=', true)
            ->where('entities.created_by', '=', user()->id);
    }

    public function visibleTemplates(bool $includeContents = false): Builder
    {
        $base = $includeContents ? $this->visibleWithContents() : $this->visibleForList();
        return $base->where('entity_page_data.template', '=', true);
    }

    protected function mergeBookSlugForSelect(array $columns): array
    {
        return array_merge($columns, ['book_slug' => function ($builder) {
            $builder->select('slug')
                ->from('entities as books')
                ->where('type', '=', 'book')
                ->whereColumn('books.id', '=', 'entities.book_id');
        }]);
    }

    public function visibleForListWithCover(): Builder
    {
        return $this->visibleForList()->with('cover');
    }
}

<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\RecordPage;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

class RecordPageQueries implements ProvidesEntityQueries
{
    protected static array $contentAttributes = [
        'name', 'id', 'slug', 'record_id', 'record_chapter_id', 'draft',
        'template', 'html', 'text', 'created_at', 'updated_at', 'priority',
        'created_by', 'updated_by', 'owned_by',
    ];
    protected static array $listAttributes = [
        'name', 'id', 'slug', 'record_id', 'record_chapter_id', 'draft',
        'template', 'text', 'created_at', 'updated_at', 'priority', 'owned_by',
    ];

    public function start(): Builder
    {
        return RecordPage::query();
    }

    public function findVisibleById(int $id): ?RecordPage
    {
        return $this->start()->scopes('visible')->find($id);
    }

    public function findVisibleByIdOrFail(int $id): RecordPage
    {
        $page = $this->findVisibleById($id);

        if (is_null($page)) {
            throw new NotFoundException(trans('errors.page_not_found'));
        }

        // dd($page->toArray());
        return $page;
    }

    public function findVisibleBySlugsOrFail(string $bookSlug, string $pageSlug): RecordPage
    {
        /** @var ?RecordPage $page */
        $page = $this->start()->with('record')
            ->scopes('visible')
            ->whereHas('record', function (Builder $query) use ($bookSlug) {
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
            ->whereHas('record', function (Builder $query) use ($bookSlug) {
                $query->where('slug', '=', $bookSlug);
            });
    }

    public function visibleForList(): Builder
    {
        return $this->start()
            ->scopes('visible')
            ->select($this->mergeBookSlugForSelect(static::$listAttributes));
    }

    public function visibleForChapterList(int $chapterId): Builder
    {
        return $this->visibleForList()
            ->where('record_chapter_id', '=', $chapterId)
            ->orderBy('draft', 'desc')
            ->orderBy('priority', 'asc');
    }

    public function visibleWithContents(): Builder
    {
        return $this->start()
            ->scopes('visible')
            ->select($this->mergeBookSlugForSelect(static::$contentAttributes));
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

    protected function mergeBookSlugForSelect(array $columns): array
    {
        return array_merge($columns, ['record_slug' => function ($builder) {
            $builder->select('slug')
                ->from('records')
                ->whereColumn('records.id', '=', 'record_pages.record_id');
        }]);
    }
}

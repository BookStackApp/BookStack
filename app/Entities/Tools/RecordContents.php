<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Record;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\RecordChapter;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Sorting\BookSortMap;
use BookStack\Sorting\BookSortMapItem;
use Illuminate\Support\Collection;

class RecordContents
{
    protected EntityQueries $queries;

    public function __construct(
        protected Record $record,
    ) {
        $this->queries = app()->make(EntityQueries::class);
    }

    /**
     * Get the current priority of the last item at the top-level of the record.
     */
    public function getLastPriority(): int
    {
        // TODO:: set maxPage via record page table 
        // $maxPage = $this->record->pages()
        //     ->where('draft', '=', false)
        //     ->where('chapter_id', '=', 0)
        //     ->max('priority');

        $maxPage = null;
            
        // dd($this->record->chapters());

        $maxChapter = $this->record->chapters()
            ->max('priority');

        return max($maxChapter, $maxPage, 1);
    }

    /**
     * Get the contents as a sorted collection tree.
     */
    public function getTree(bool $showDrafts = false, bool $renderPages = false): Collection
    {
        $pages = $this->getPages($showDrafts, $renderPages);
        $chapters = $this->record->chapters()->scopes('visible')->get();
        // $chapters = $this->record->chapters();
        // dd($chapters);
        $all = collect()->concat($pages)->concat($chapters);
        $chapterMap = $chapters->keyBy('id');
        $lonePages = collect();

        $pages->groupBy('record_chapter_id')->each(function ($pages, $chapter_id) use ($chapterMap, &$lonePages) {
            $chapter = $chapterMap->get($chapter_id);
            if ($chapter) {
                $chapter->setAttribute('visible_pages', collect($pages)->sortBy($this->bookChildSortFunc()));
            } else {
                $lonePages = $lonePages->concat($pages);
            }
        });
        
        $chapters->whereNull('visible_pages')->each(function (RecordChapter $chapter) {
            $chapter->setAttribute('visible_pages', collect([]));
        });
        // dd('kk');
        
        $all->each(function (Entity $entity) use ($renderPages) {
            $entity->setRelation('record', $this->record);
            
            if ($renderPages && $entity instanceof Page) {
                $entity->html = (new PageContent($entity))->render();
            }
        });

        return collect($chapters)->concat($lonePages)->sortBy($this->bookChildSortFunc());
    }

    /**
     * Function for providing a sorting score for an entity in relation to the
     * other items within the record.
     */
    protected function bookChildSortFunc(): callable
    {
        return function (Entity $entity) {
            if (isset($entity['draft']) && $entity['draft']) {
                return -100;
            }

            return $entity['priority'] ?? 0;
        };
    }

    /**
     * Get the visible pages within this record.
     */
    protected function getPages(bool $showDrafts = false, bool $getPageContent = false): Collection
    {
        if ($getPageContent) {
            $query = $this->queries->recordPages->visibleWithContents();
        } else {
            $query = $this->queries->recordPages->visibleForList();
        }

        if (!$showDrafts) {
            $query->where('draft', '=', false);
        }

        return $query->where('record_id', '=', $this->record->id)->get();
    }
}

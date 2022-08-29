<?php

namespace BookStack\References;

use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Collection;

class ReferenceStore
{

    /**
     * Update the outgoing references for the given page.
     */
    public function updateForPage(Page $page): void
    {
        $this->updateForPages([$page]);
    }

    /**
     * Update the outgoing references for all pages in the system.
     */
    public function updateForAllPages(): void
    {
        Reference::query()
            ->where('from_type', '=', (new Page())->getMorphClass())
            ->delete();

        Page::query()->select(['id', 'html'])->chunk(100, function(Collection $pages) {
            $this->updateForPages($pages->all());
        });
    }

    /**
     * Update the outgoing references for the pages in the given array.
     *
     * @param Page[] $pages
     */
    protected function updateForPages(array $pages): void
    {
        if (count($pages) === 0) {
            return;
        }

        $parser = CrossLinkParser::createWithEntityResolvers();
        $references = [];

        $pageIds = array_map(fn(Page $page) => $page->id, $pages);
        Reference::query()
            ->where('from_type', '=', $pages[0]->getMorphClass())
            ->whereIn('from_id', $pageIds)
            ->delete();

        foreach ($pages as $page) {
            $models = $parser->extractLinkedModels($page->html);

            foreach ($models as $model) {
                $references[] = [
                    'from_id' => $page->id,
                    'from_type' => $page->getMorphClass(),
                    'to_id' => $model->id,
                    'to_type' => $model->getMorphClass(),
                ];
            }
        }

        foreach (array_chunk($references, 1000) as $referenceDataChunk) {
            Reference::query()->insert($referenceDataChunk);
        }
    }

}
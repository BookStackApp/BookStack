<?php

namespace BookStack\References;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\RevisionRepo;

class CrossLinkReplacer
{
    protected ReferenceFetcher $referenceFetcher;
    protected RevisionRepo $revisionRepo;

    public function __construct(ReferenceFetcher $referenceFetcher, RevisionRepo $revisionRepo)
    {
        $this->referenceFetcher = $referenceFetcher;
        $this->revisionRepo = $revisionRepo;
    }

    public function updateEntityPageReferences(Entity $entity, string $oldLink)
    {
        $references = $this->referenceFetcher->getPageReferencesToEntity($entity);
        $newLink = $entity->getUrl();

        /** @var Reference $reference */
        foreach ($references as $reference) {
            /** @var Page $page */
            $page = $reference->from;
            $this->updateReferencesWithinPage($page, $oldLink, $newLink);
        }
    }

    protected function updateReferencesWithinPage(Page $page, string $oldLink, string $newLink)
    {
        $page = (clone $page)->refresh();
        $html = '';// TODO - Update HTML content
        $markdown = '';// TODO - Update markdown content

        $page->html = $html;
        $page->markdown = $markdown;
        $page->revision_count++;
        $page->save();

        $summary = ''; // TODO - Get default summary from translations
        $this->revisionRepo->storeNewForPage($page, $summary);
    }
}
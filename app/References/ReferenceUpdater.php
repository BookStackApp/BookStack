<?php

namespace BookStack\References;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\HasHtmlDescription;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\RevisionRepo;
use BookStack\Util\HtmlDocument;

class ReferenceUpdater
{
    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
        protected RevisionRepo $revisionRepo,
    ) {
    }

    public function updateEntityReferences(Entity $entity, string $oldLink): void
    {
        $references = $this->getReferencesToUpdate($entity);
        $newLink = $entity->getUrl();

        foreach ($references as $reference) {
            /** @var Entity $entity */
            $entity = $reference->from;
            $this->updateReferencesWithinEntity($entity, $oldLink, $newLink);
        }
    }

    /**
     * @return Reference[]
     */
    protected function getReferencesToUpdate(Entity $entity): array
    {
        /** @var Reference[] $references */
        $references = $this->referenceFetcher->getReferencesToEntity($entity)->values()->all();

        if ($entity instanceof Book) {
            $pages = $entity->pages()->get(['id']);
            $chapters = $entity->chapters()->get(['id']);
            $children = $pages->concat($chapters);
            foreach ($children as $bookChild) {
                /** @var Reference[] $childRefs */
                $childRefs = $this->referenceFetcher->getReferencesToEntity($bookChild)->values()->all();
                array_push($references, ...$childRefs);
            }
        }

        $deduped = [];
        foreach ($references as $reference) {
            $key = $reference->from_id . ':' . $reference->from_type;
            $deduped[$key] = $reference;
        }

        return array_values($deduped);
    }

    protected function updateReferencesWithinEntity(Entity $entity, string $oldLink, string $newLink): void
    {
        if ($entity instanceof Page) {
            $this->updateReferencesWithinPage($entity, $oldLink, $newLink);
            return;
        }

        if (in_array(HasHtmlDescription::class, class_uses($entity))) {
            $this->updateReferencesWithinDescription($entity, $oldLink, $newLink);
        }
    }

    protected function updateReferencesWithinDescription(Entity $entity, string $oldLink, string $newLink): void
    {
        /** @var HasHtmlDescription&Entity $entity */
        $entity = (clone $entity)->refresh();
        $html = $this->updateLinksInHtml($entity->description_html ?: '', $oldLink, $newLink);
        $entity->description_html = $html;
        $entity->save();
    }

    protected function updateReferencesWithinPage(Page $page, string $oldLink, string $newLink): void
    {
        $page = (clone $page)->refresh();
        $html = $this->updateLinksInHtml($page->html, $oldLink, $newLink);
        $markdown = $this->updateLinksInMarkdown($page->markdown, $oldLink, $newLink);

        $page->html = $html;
        $page->markdown = $markdown;
        $page->revision_count++;
        $page->save();

        $summary = trans('entities.pages_references_update_revision');
        $this->revisionRepo->storeNewForPage($page, $summary);
    }

    protected function updateLinksInMarkdown(string $markdown, string $oldLink, string $newLink): string
    {
        if (empty($markdown)) {
            return $markdown;
        }

        $commonLinkRegex = '/(\[.*?\]\()' . preg_quote($oldLink, '/') . '(.*?\))/i';
        $markdown = preg_replace($commonLinkRegex, '$1' . $newLink . '$2', $markdown);

        $referenceLinkRegex = '/(\[.*?\]:\s?)' . preg_quote($oldLink, '/') . '(.*?)($|\s)/i';
        $markdown = preg_replace($referenceLinkRegex, '$1' . $newLink . '$2$3', $markdown);

        return $markdown;
    }

    protected function updateLinksInHtml(string $html, string $oldLink, string $newLink): string
    {
        if (empty($html)) {
            return $html;
        }

        $doc = new HtmlDocument($html);
        $anchors = $doc->queryXPath('//a[@href]');

        /** @var \DOMElement $anchor */
        foreach ($anchors as $anchor) {
            $link = $anchor->getAttribute('href');
            $updated = str_ireplace($oldLink, $newLink, $link);
            $anchor->setAttribute('href', $updated);
        }

        return $doc->getBodyInnerHtml();
    }
}

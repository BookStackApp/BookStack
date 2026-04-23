<?php

namespace BookStack\References;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\HasDescriptionInterface;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Facades\DB;
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
     * Change existing references for a range of entities using the given context.
     */
    public function changeReferencesUsingContext(ReferenceChangeContext $context): void
    {
        $bindings = [];
        foreach ($context->getOldEntities() as $old) {
            $bindings[] = $old->getMorphClass();
            $bindings[] = $old->id;
        }

        // No targets to update within the context, so no need to continue.
        if (count($bindings) < 2) {
            return;
        }

        $toReferenceQuery = '(to_type, to_id) IN (' . rtrim(str_repeat('(?,?),', count($bindings) / 2), ',') . ')';

        // Cycle each new entity in the context
        foreach ($context->getNewEntities() as $new) {
            // For each, get all references from it which lead to other items within the context of the change
            $newReferencesInContext = $new->referencesFrom()->whereRaw($toReferenceQuery, $bindings)->get();
            // For each reference, update the URL and the reference entry
            foreach ($newReferencesInContext as $reference) {
                $oldToEntity = $reference->to;
                $newToEntity = $context->getNewForOld($oldToEntity);
                if ($newToEntity === null) {
                    continue;
                }

                $this->updateReferencesWithinEntity($new, $oldToEntity->getUrl(), $newToEntity->getUrl());
                if ($newToEntity instanceof Page && $oldToEntity instanceof Page) {
                    $this->updateReferencesWithinEntity($new, $oldToEntity->getPermalink(), $newToEntity->getPermalink());
                }
                $reference->to_id = $newToEntity->id;
                $reference->to_type = $newToEntity->getMorphClass();
                $reference->save();
            }

            // Handle embedded HTML links within content fields
            foreach ($context->getUrlMap() as $oldUrl => $newUrl) {
                if ($new instanceof Page) {
                    $this->updateReferencesWithinPage($new, $oldUrl, $newUrl);
                }
                // Only for Chapter, Book, or Bookshelf - entities with descriptions
                if (($new instanceof Chapter) || ($new instanceof Book) || ($new instanceof Bookshelf)) {
                    $this->updateReferencesWithinDescription($new, $oldUrl, $newUrl);
                }
            }
        }
    }

    /**
     * @return Reference[]
     */
    protected function getReferencesToUpdate(Entity $entity): array
    {
        /** @var Reference[] $references */
        $references = $this->referenceFetcher->getReferencesToEntity($entity, true)->values()->all();

        if ($entity instanceof Book) {
            $pages = $entity->pages()->get(['id']);
            $chapters = $entity->chapters()->get(['id']);
            $children = $pages->concat($chapters);
            foreach ($children as $bookChild) {
                /** @var Reference[] $childRefs */
                $childRefs = $this->referenceFetcher->getReferencesToEntity($bookChild, true)->values()->all();
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
        }

        if (($entity instanceof Chapter) || ($entity instanceof Book) || ($entity instanceof Bookshelf)) {
            $this->updateReferencesWithinDescription($entity, $oldLink, $newLink);
        }
    }

    protected function updateReferencesWithinDescription(Entity&HasDescriptionInterface $entity, string $oldLink, string $newLink): void
    {
        $description = $entity->descriptionInfo();
        $originalHtml = $description->getHtml(true) ?: '';
        $html = $this->updateLinksInHtml($originalHtml, $oldLink, $newLink);
        $description->set($html);

        $plainText = $description->getPlain();
        $entity->description_html = $html;
        $entity->description = $plainText;

        $existing = DB::table('entity_container_data')
            ->where('entity_id', $entity->id)
            ->where('entity_type', $entity->getMorphClass())
            ->first();

        if ($existing) {
            DB::table('entity_container_data')
                ->where('entity_id', $entity->id)
                ->where('entity_type', $entity->getMorphClass())
                ->update([
                    'description_html' => $html,
                    'description' => $plainText,
                ]);
        } else {
            DB::table('entity_container_data')->insert([
                'entity_id' => $entity->id,
                'entity_type' => $entity->getMorphClass(),
                'description_html' => $html,
                'description' => $plainText,
            ]);
        }
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
            $isExactMatch = strcasecmp($link, $oldLink) === 0;
            $isPrefixMatch = strcasecmp(substr($link, 0, strlen($oldLink)), $oldLink) === 0 && strlen($link) > strlen($oldLink) && $link[strlen($oldLink)] === '/';
            if (!$isExactMatch && !$isPrefixMatch) {
                continue;
            }
            $anchor->setAttribute('href', $newLink . ($isPrefixMatch ? substr($link, strlen($oldLink)) : ''));
        }

        $result = $doc->getBodyInnerHtml();
        return $result;
    }
}

<?php

namespace BookStack\References;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\RevisionRepo;
use DOMDocument;
use DOMXPath;

class ReferenceUpdater
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

        $html = '<body>' . $html . '</body>';
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $xPath = new DOMXPath($doc);
        $anchors = $xPath->query('//a[@href]');

        /** @var \DOMElement $anchor */
        foreach ($anchors as $anchor) {
            $link = $anchor->getAttribute('href');
            $updated = str_ireplace($oldLink, $newLink, $link);
            $anchor->setAttribute('href', $updated);
        }

        $html = '';
        $topElems = $doc->documentElement->childNodes->item(0)->childNodes;
        foreach ($topElems as $child) {
            $html .= $doc->saveHTML($child);
        }

        return $html;
    }
}
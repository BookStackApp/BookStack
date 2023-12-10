<?php

namespace BookStack\Entities\Tools;

use BookStack\Util\HtmlDocument;
use Closure;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class PageIncludeParser
{
    protected static string $includeTagRegex = "/{{@\s?([0-9].*?)}}/";

    /**
     * Elements to clean up and remove if left empty after a parsing operation.
     * @var DOMElement[]
     */
    protected array $toCleanup = [];

    /**
     * @param Closure(PageIncludeTag $tag): PageContent $pageContentForId
     */
    public function __construct(
        protected HtmlDocument $doc,
        protected Closure $pageContentForId,
    ) {
    }

    /**
     * Parse out the include tags.
     * Returns the count of new content DOM nodes added to the document.
     */
    public function parse(): int
    {
        $nodesAdded = 0;
        $tags = $this->locateAndIsolateIncludeTags();

        foreach ($tags as $tag) {
            /** @var PageIncludeContent $content */
            $content = $this->pageContentForId->call($this, $tag);

            if (!$content->isInline()) {
                $parentP = $this->getParentParagraph($tag->domNode);
                $isWithinParentP = $parentP === $tag->domNode->parentNode;
                if ($parentP && $isWithinParentP) {
                    $this->splitNodeAtChildNode($tag->domNode->parentNode, $tag->domNode);
                } else if ($parentP) {
                    $this->moveTagNodeToBesideParent($tag, $parentP);
                }
            }

            $replacementNodes = $content->toDomNodes();
            $nodesAdded += count($replacementNodes);
            $this->replaceNodeWithNodes($tag->domNode, $replacementNodes);
        }

        $this->cleanup();

        return $nodesAdded;
    }

    /**
     * Locate include tags within the given document, isolating them to their
     * own nodes in the DOM for future targeted manipulation.
     * @return PageIncludeTag[]
     */
    protected function locateAndIsolateIncludeTags(): array
    {
        $includeHosts = $this->doc->queryXPath("//*[text()[contains(., '{{@')]]");
        $includeTags = [];

        /** @var DOMNode $node */
        foreach ($includeHosts as $node) {
            /** @var DOMNode $childNode */
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeName === '#text') {
                    array_push($includeTags, ...$this->splitTextNodesAtTags($childNode));
                }
            }
        }

        return $includeTags;
    }

    /**
     * Takes a text DOMNode and splits its text content at include tags
     * into multiple text nodes within the original parent.
     * Returns found PageIncludeTag references.
     * @return PageIncludeTag[]
     */
    protected function splitTextNodesAtTags(DOMNode $textNode): array
    {
        $includeTags = [];
        $text = $textNode->textContent;
        preg_match_all(static::$includeTagRegex, $text, $matches, PREG_OFFSET_CAPTURE);

        $currentOffset = 0;
        foreach ($matches[0] as $index => $fullTagMatch) {
            $tagOuterContent = $fullTagMatch[0];
            $tagInnerContent = $matches[1][$index][0];
            $tagStartOffset = $fullTagMatch[1];

            if ($currentOffset < $tagStartOffset) {
                $previousText = substr($text, $currentOffset, $tagStartOffset - $currentOffset);
                $textNode->parentNode->insertBefore(new DOMText($previousText), $textNode);
            }

            $node = $textNode->parentNode->insertBefore(new DOMText($tagOuterContent), $textNode);
            $includeTags[] = new PageIncludeTag($tagInnerContent, $node);
            $currentOffset = $tagStartOffset + strlen($tagOuterContent);
        }

        if ($currentOffset > 0) {
            $textNode->textContent = substr($text, $currentOffset);
        }

        return $includeTags;
    }

    /**
     * Replace the given node with all those in $replacements
     * @param DOMNode[] $replacements
     */
    protected function replaceNodeWithNodes(DOMNode $toReplace, array $replacements): void
    {
        /** @var DOMDocument $targetDoc */
        $targetDoc = $toReplace->ownerDocument;

        foreach ($replacements as $replacement) {
            if ($replacement->ownerDocument !== $targetDoc) {
                $replacement = $targetDoc->importNode($replacement, true);
            }

            $toReplace->parentNode->insertBefore($replacement, $toReplace);
        }

        $toReplace->parentNode->removeChild($toReplace);
    }

    /**
     * Move a tag node to become a sibling of the given parent.
     * Will attempt to guess a position based upon the tag content within the parent.
     */
    protected function moveTagNodeToBesideParent(PageIncludeTag $tag, DOMNode $parent): void
    {
        $parentText = $parent->textContent;
        $tagPos = strpos($parentText, $tag->tagContent);
        $before = $tagPos < (strlen($parentText) / 2);
        $this->toCleanup[] = $tag->domNode->parentNode;

        if ($before) {
            $parent->parentNode->insertBefore($tag->domNode, $parent);
        } else {
            $parent->parentNode->insertBefore($tag->domNode, $parent->nextSibling);
        }
    }

    /**
     * Splits the given $parentNode at the location of the $domNode within it.
     * Attempts replicate the original $parentNode, moving some of their parent
     * children in where needed, before adding the $domNode between.
     */
    protected function splitNodeAtChildNode(DOMElement $parentNode, DOMNode $domNode): void
    {
        $children = [...$parentNode->childNodes];
        $splitPos = array_search($domNode, $children, true);
        if ($splitPos === false) {
            $splitPos = count($children) - 1;
        }

        $parentClone = $parentNode->cloneNode();
        $parentNode->parentNode->insertBefore($parentClone, $parentNode);
        $parentClone->removeAttribute('id');

        for ($i = 0; $i < $splitPos; $i++) {
            /** @var DOMNode $child */
            $child = $children[$i];
            $parentClone->appendChild($child);
        }

        $parentNode->parentNode->insertBefore($domNode, $parentNode);

        $this->toCleanup[] = $parentNode;
        $this->toCleanup[] = $parentClone;
    }

    /**
     * Get the parent paragraph of the given node, if existing.
     */
    protected function getParentParagraph(DOMNode $parent): ?DOMNode
    {
        do {
            if (strtolower($parent->nodeName) === 'p') {
                return $parent;
            }

            $parent = $parent->parentNode;
        } while ($parent !== null);

        return null;
    }

    /**
     * Cleanup after a parse operation.
     * Removes stranded elements we may have left during the parse.
     */
    protected function cleanup(): void
    {
        foreach ($this->toCleanup as $element) {
            $element->normalize();
            while ($element->parentNode && !$element->hasChildNodes()) {
                $parent = $element->parentNode;
                $parent->removeChild($element);
                $element = $parent;
            }
        }
    }
}

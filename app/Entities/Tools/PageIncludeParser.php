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

    public function __construct(
        protected string $pageHtml,
        protected Closure $pageContentForId,
    ) {
    }

    public function parse(): string
    {
        $doc = new HtmlDocument($this->pageHtml);

        $tags = $this->locateAndIsolateIncludeTags($doc);
        $topLevel = [...$doc->getBodyChildren()];

        foreach ($tags as $tag) {
            $htmlContent = $this->pageContentForId->call($this, $tag->getPageId());
            $content = new PageIncludeContent($htmlContent, $tag);

            if (!$content->isInline()) {
                $isParentTopLevel = in_array($tag->domNode->parentNode, $topLevel, true);
                if ($isParentTopLevel) {
                    $this->splitNodeAtChildNode($tag->domNode->parentNode, $tag->domNode);
                } else {
                    $this->promoteTagNodeToBody($tag, $doc->getBody());
                }
            }

            $this->replaceNodeWithNodes($tag->domNode, $content->toDomNodes());
        }

        // TODO Notes: May want to eventually parse through backwards, which should avoid issues
        //   in changes affecting the next tag, where tags may be in the same/adjacent nodes.

        return $doc->getBodyInnerHtml();
    }

    /**
     * Locate include tags within the given document, isolating them to their
     * own nodes in the DOM for future targeted manipulation.
     * @return PageIncludeTag[]
     */
    protected function locateAndIsolateIncludeTags(HtmlDocument $doc): array
    {
        $includeHosts = $doc->queryXPath("//body//*[contains(text(), '{{@')]");
        $includeTags = [];

        /** @var DOMNode $node */
        /** @var DOMNode $childNode */
        foreach ($includeHosts as $node) {
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
     * @param DOMNode[] $replacements
     */
    protected function replaceNodeWithNodes(DOMNode $toReplace, array $replacements): void
    {
        /** @var DOMDocument $targetDoc */
        $targetDoc = $toReplace->ownerDocument;

        foreach ($replacements as $replacement) {
            if ($replacement->ownerDocument !== $targetDoc) {
                $replacement = $targetDoc->adoptNode($replacement);
            }

            $toReplace->parentNode->insertBefore($replacement, $toReplace);
        }

        $toReplace->parentNode->removeChild($toReplace);
    }

    protected function promoteTagNodeToBody(PageIncludeTag $tag, DOMNode $body): void
    {
        /** @var DOMNode $topParent */
        $topParent = $tag->domNode->parentNode;
        while ($topParent->parentNode !== $body) {
            $topParent = $topParent->parentNode;
        }

        $parentText = $topParent->textContent;
        $tagPos = strpos($parentText, $tag->tagContent);
        $before = $tagPos < (strlen($parentText) / 2);

        if ($before) {
            $body->insertBefore($tag->domNode, $topParent);
        } else {
            $body->insertBefore($tag->domNode, $topParent->nextSibling);
        }
    }

    protected function splitNodeAtChildNode(DOMElement $parentNode, DOMNode $domNode): void
    {
        $children = [...$parentNode->childNodes];
        $splitPos = array_search($domNode, $children, true) ?: count($children);
        $parentClone = $parentNode->cloneNode();
        $parentClone->removeAttribute('id');

        /** @var DOMNode $child */
        for ($i = 0; $i < $splitPos; $i++) {
            $child = $children[0];
            $parentClone->appendChild($child);
        }

        if ($parentClone->hasChildNodes()) {
            $parentNode->parentNode->insertBefore($parentClone, $parentNode);
        }

        $parentNode->parentNode->insertBefore($domNode, $parentNode);

        $parentClone->normalize();
        $parentNode->normalize();
        if (!$parentNode->hasChildNodes()) {
            $parentNode->remove();
        }
        if (!$parentClone->hasChildNodes()) {
            $parentClone->remove();
        }
    }
}

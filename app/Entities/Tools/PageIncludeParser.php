<?php

namespace BookStack\Entities\Tools;

use BookStack\Util\HtmlDocument;
use Closure;
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

        foreach ($tags as $tag) {
            $htmlContent = $this->pageContentForId->call($this, $tag->getPageId());
            $content = new PageIncludeContent($htmlContent, $tag);

            if ($content->isInline()) {
                $adopted = $doc->adoptNodes($content->toDomNodes());
                foreach ($adopted as $adoptedContentNode) {
                    $tag->domNode->parentNode->insertBefore($adoptedContentNode, $tag->domNode);
                }
                $tag->domNode->parentNode->removeChild($tag->domNode);
                continue;
            }

            // TODO - Non-inline
        }

        // TODO:
        // Hunt down the specific text nodes with matches
        // Split out tag text node from rest of content
        // Fetch tag content->
          // If range or top-block: delete tag text node, [Promote to top-block], delete old top-block if empty
          // If inline: Replace current text node with new text or elem
        // !! "Range" or "inline" status should come from tag parser and content fetcher, not guessed direct from content
        //     since we could have a range of inline elements

        // [Promote to top-block]
        // Tricky operation.
        // Can throw in before or after current top-block depending on relative position
        // Could [Split] top-block but complex past a single level depth.
        // Maybe [Split] if one level depth, otherwise default to before/after block
        // Should work for the vast majority of cases, and not for those which would
        // technically be invalid in-editor anyway.

        // [Split]
        // Copy original top-block node type and attrs (apart from ID)
        // Move nodes after promoted tag-node into copy
        // Insert copy after original (after promoted top-block eventually)

        // Notes: May want to eventually parse through backwards, which should avoid issues
        // in changes affecting the next tag, where tags may be in the same/adjacent nodes.


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
}

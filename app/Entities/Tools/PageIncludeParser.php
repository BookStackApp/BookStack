<?php

namespace BookStack\Entities\Tools;

use BookStack\Util\HtmlDocument;
use Closure;

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
        $html = new HtmlDocument($this->pageHtml);

        $includeHosts = $html->queryXPath("//body//*[contains(text(), '{{@')]");
        $node = $includeHosts->item(0);

        // One of the direct child textnodes of the "$includeHosts" should be
        // the one with the include tag within.
        $textNode = $node->childNodes->item(0);

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


        return $html->getBodyInnerHtml();
    }
}

<?php

namespace BookStack\Util;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

class HtmlNonceApplicator
{
    /**
     * Apply the given nonce to all scripts and styles in the given html.
     */
    public static function apply(string $html, string $nonce): string
    {
        if (empty($html)) {
            return $html;
        }

        $html = '<body>' . $html . '</body>';
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);

        // Apply to scripts
        $scriptElems = $xPath->query('//script');
        static::addNonceAttributes($scriptElems, $nonce);

        // Apply to styles
        $styleElems = $xPath->query('//style');
        static::addNonceAttributes($styleElems, $nonce);

        $returnHtml = '';
        $topElems = $doc->documentElement->childNodes->item(0)->childNodes;
        foreach ($topElems as $child) {
            $returnHtml .= $doc->saveHTML($child);
        }

        return $returnHtml;
    }

    protected static function addNonceAttributes(DOMNodeList $nodes, string $nonce): void
    {
        /** @var DOMElement $node */
        foreach ($nodes as $node) {
            $node->setAttribute('nonce', $nonce);
        }
    }

}

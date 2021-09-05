<?php

namespace BookStack\Util;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

class HtmlNonceApplicator
{
    protected static $placeholder = '[CSP_NONCE_VALUE]';

    /**
     * Prepare the given HTML content with nonce attributes including a placeholder
     * value which we can target later.
     */
    public static function prepare(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        $html = '<body>' . $html . '</body>';
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_SCHEMA_CREATE);
        $xPath = new DOMXPath($doc);

        // Apply to scripts
        $scriptElems = $xPath->query('//script');
        static::addNonceAttributes($scriptElems, static::$placeholder);

        // Apply to styles
        $styleElems = $xPath->query('//style');
        static::addNonceAttributes($styleElems, static::$placeholder);

        $returnHtml = '';
        $topElems = $doc->documentElement->childNodes->item(0)->childNodes;
        foreach ($topElems as $child) {
            $content =  $doc->saveHTML($child);
            $returnHtml .= $content;
        }

        return $returnHtml;
    }

    /**
     * Apply the give nonce value to the given prepared HTML.
     */
    public static function apply(string $html, string $nonce): string
    {
        return str_replace(static::$placeholder, $nonce, $html);
    }

    protected static function addNonceAttributes(DOMNodeList $nodes, string $attrValue): void
    {
        /** @var DOMElement $node */
        foreach ($nodes as $node) {
            $node->setAttribute('nonce', $attrValue);
        }
    }

}

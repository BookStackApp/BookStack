<?php

namespace BookStack\Util;

use DOMElement;
use DOMNodeList;

class HtmlNonceApplicator
{
    protected static string $placeholder = '[CSP_NONCE_VALUE]';

    /**
     * Prepare the given HTML content with nonce attributes including a placeholder
     * value which we can target later.
     */
    public static function prepare(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        // LIBXML_SCHEMA_CREATE was found to be required here otherwise
        // the PHP DOMDocument handling will attempt to format/close
        // HTML tags within scripts and therefore change JS content.
        $doc = new HtmlDocument($html, LIBXML_SCHEMA_CREATE);

        // Apply to scripts
        $scriptElems = $doc->queryXPath('//script');
        static::addNonceAttributes($scriptElems, static::$placeholder);

        // Apply to styles
        $styleElems = $doc->queryXPath('//style');
        static::addNonceAttributes($styleElems, static::$placeholder);

        return $doc->getBodyInnerHtml();
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

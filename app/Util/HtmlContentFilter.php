<?php

namespace BookStack\Util;

use DOMDocument;
use DOMNodeList;
use DOMXPath;

class HtmlContentFilter
{
    /**
     * Remove all of the script elements from the given HTML.
     */
    public static function removeScripts(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        $html = '<body>' . $html . '</body>';
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);

        // Remove standard script tags
        $scriptElems = $xPath->query('//script');
        static::removeNodes($scriptElems);

        // Remove clickable links to JavaScript URI
        $badLinks = $xPath->query('//*[' . static::xpathContains('@href', 'javascript:') . ']');
        static::removeNodes($badLinks);

        // Remove forms with calls to JavaScript URI
        $badForms = $xPath->query('//*[' . static::xpathContains('@action', 'javascript:') . '] | //*[' . static::xpathContains('@formaction', 'javascript:') . ']');
        static::removeNodes($badForms);

        // Remove meta tag to prevent external redirects
        $metaTags = $xPath->query('//meta[' . static::xpathContains('@content', 'url') . ']');
        static::removeNodes($metaTags);

        // Remove data or JavaScript iFrames
        $badIframes = $xPath->query('//*[' . static::xpathContains('@src', 'data:') . '] | //*[' . static::xpathContains('@src', 'javascript:') . '] | //*[@srcdoc]');
        static::removeNodes($badIframes);

        // Remove 'on*' attributes
        $onAttributes = $xPath->query('//@*[starts-with(name(), \'on\')]');
        foreach ($onAttributes as $attr) {
            /** @var \DOMAttr $attr */
            $attrName = $attr->nodeName;
            $attr->parentNode->removeAttribute($attrName);
        }

        $html = '';
        $topElems = $doc->documentElement->childNodes->item(0)->childNodes;
        foreach ($topElems as $child) {
            $html .= $doc->saveHTML($child);
        }

        return $html;
    }

    /**
     * Create a xpath contains statement with a translation automatically built within
     * to affectively search in a cases-insensitive manner.
     */
    protected static function xpathContains(string $property, string $value): string
    {
        $value = strtolower($value);
        $upperVal = strtoupper($value);
        return 'contains(translate(' . $property . ', \'' . $upperVal . '\', \'' . $value . '\'), \'' . $value . '\')';
    }

    /**
     * Removed all of the given DOMNodes.
     */
    protected static function removeNodes(DOMNodeList $nodes): void
    {
        foreach ($nodes as $node) {
            $node->parentNode->removeChild($node);
        }
    }
}

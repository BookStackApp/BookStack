<?php

namespace BookStack\Util;

use DOMAttr;
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

        // Remove elements with a xlink:href attribute
        // Used in SVG but deprecated anyway, so we'll be a bit more heavy-handed here.
        $xlinkHrefAttributes = $xPath->query('//@*[contains(name(), \'xlink:href\')]');
        static::removeAttributes($xlinkHrefAttributes);

        // Remove 'on*' attributes
        $onAttributes = $xPath->query('//@*[starts-with(name(), \'on\')]');
        static::removeAttributes($onAttributes);

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
     * Remove all the given DOMNodes.
     */
    protected static function removeNodes(DOMNodeList $nodes): void
    {
        foreach ($nodes as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    /**
     * Remove all the given attribute nodes.
     */
    protected static function removeAttributes(DOMNodeList $attrs): void
    {
        /** @var DOMAttr $attr */
        foreach ($attrs as $attr) {
            $attrName = $attr->nodeName;
            $attr->parentNode->removeAttribute($attrName);
        }
    }
}

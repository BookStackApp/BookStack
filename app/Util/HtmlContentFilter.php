<?php

namespace BookStack\Util;

use DOMAttr;
use DOMElement;
use DOMNodeList;

class HtmlContentFilter
{
    /**
     * Remove all the script elements from the given HTML document.
     */
    public static function removeScriptsFromDocument(HtmlDocument $doc)
    {
        // Remove standard script tags
        $scriptElems = $doc->queryXPath('//script');
        static::removeNodes($scriptElems);

        // Remove clickable links to JavaScript URI
        $badLinks = $doc->queryXPath('//*[' . static::xpathContains('@href', 'javascript:') . ']');
        static::removeNodes($badLinks);

        // Remove forms with calls to JavaScript URI
        $badForms = $doc->queryXPath('//*[' . static::xpathContains('@action', 'javascript:') . '] | //*[' . static::xpathContains('@formaction', 'javascript:') . ']');
        static::removeNodes($badForms);

        // Remove meta tag to prevent external redirects
        $metaTags = $doc->queryXPath('//meta[' . static::xpathContains('@content', 'url') . ']');
        static::removeNodes($metaTags);

        // Remove data or JavaScript iFrames
        $badIframes = $doc->queryXPath('//*[' . static::xpathContains('@src', 'data:') . '] | //*[' . static::xpathContains('@src', 'javascript:') . '] | //*[@srcdoc]');
        static::removeNodes($badIframes);

        // Remove attributes, within svg children, hiding JavaScript or data uris.
        // A bunch of svg element and attribute combinations expose xss possibilities.
        // For example, SVG animate tag can exploit javascript in values.
        $badValuesAttrs = $doc->queryXPath('//svg//@*[' . static::xpathContains('.', 'data:') . '] | //svg//@*[' . static::xpathContains('.', 'javascript:') . ']');
        static::removeAttributes($badValuesAttrs);

        // Remove elements with a xlink:href attribute
        // Used in SVG but deprecated anyway, so we'll be a bit more heavy-handed here.
        $xlinkHrefAttributes = $doc->queryXPath('//@*[contains(name(), \'xlink:href\')]');
        static::removeAttributes($xlinkHrefAttributes);

        // Remove 'on*' attributes
        $onAttributes = $doc->queryXPath('//@*[starts-with(name(), \'on\')]');
        static::removeAttributes($onAttributes);
    }

    /**
     * Remove scripts from the given HTML string.
     */
    public static function removeScriptsFromHtmlString(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        $doc = new HtmlDocument($html);
        static::removeScriptsFromDocument($doc);

        return $doc->getBodyInnerHtml();
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
            /** @var DOMElement $parentNode */
            $parentNode = $attr->parentNode;
            $parentNode->removeAttribute($attrName);
        }
    }
}

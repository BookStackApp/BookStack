<?php

namespace BookStack\Util;

use DOMAttr;
use DOMElement;
use DOMNodeList;

class HtmlContentFilter
{
    public function __construct(
        protected HtmlContentFilterConfig $config
    ) {
    }

    public function filterDocument(HtmlDocument $doc): string
    {
        if ($this->config->filterOutJavaScript) {
            $this->filterOutScriptsFromDocument($doc);
        }
        if ($this->config->filterOutFormElements) {
            $this->filterOutFormElementsFromDocument($doc);
        }
        if ($this->config->filterOutBadHtmlElements) {
            $this->filterOutBadHtmlElementsFromDocument($doc);
        }
        if ($this->config->filterOutNonContentElements) {
            $this->filterOutNonContentElementsFromDocument($doc);
        }

        $filtered = $doc->getBodyInnerHtml();
        if ($this->config->useAllowListFilter) {
            $filtered = $this->applyAllowListFiltering($filtered);
        }

        return $filtered;
    }

    public function filterString(string $html): string
    {
        return $this->filterDocument(new HtmlDocument($html));
    }

    protected function applyAllowListFiltering(string $html): string
    {
        $purifier = new ConfiguredHtmlPurifier();
        return $purifier->purify($html);
    }

    protected function filterOutScriptsFromDocument(HtmlDocument $doc): void
    {
        // Remove standard script tags
        $scriptElems = $doc->queryXPath('//script');
        static::removeNodes($scriptElems);

        // Remove clickable links to JavaScript URI
        $badLinks = $doc->queryXPath('//*[' . static::xpathContains('@href', 'javascript:') . ']');
        static::removeNodes($badLinks);

        // Remove elements with form-like attributes with calls to JavaScript URI
        $badForms = $doc->queryXPath('//*[' . static::xpathContains('@action', 'javascript:') . '] | //*[' . static::xpathContains('@formaction', 'javascript:') . ']');
        static::removeNodes($badForms);

        // Remove data or JavaScript iFrames & embeds
        $badIframes = $doc->queryXPath('//*[' . static::xpathContains('@src', 'data:') . '] | //*[' . static::xpathContains('@src', 'javascript:') . '] | //*[@srcdoc]');
        static::removeNodes($badIframes);

        // Remove data or JavaScript objects
        $badObjects = $doc->queryXPath('//*[' . static::xpathContains('@data', 'data:') . '] | //*[' . static::xpathContains('@data', 'javascript:') . ']');
        static::removeNodes($badObjects);

        // Remove attributes, within svg children, hiding JavaScript or data uris.
        // A bunch of svg element and attribute combinations expose xss possibilities.
        // For example, SVG animate tag can exploit JavaScript in values.
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

    protected function filterOutFormElementsFromDocument(HtmlDocument $doc): void
    {
        // Remove form elements
        $formElements = ['form', 'fieldset', 'button', 'textarea', 'select'];
        foreach ($formElements as $formElement) {
            $matchingFormElements = $doc->queryXPath('//' . $formElement);
            static::removeNodes($matchingFormElements);
        }

        // Remove non-checkbox inputs
        $inputsToRemove = $doc->queryXPath('//input');
        /** @var DOMElement $input */
        foreach ($inputsToRemove as $input) {
            $type = strtolower($input->getAttribute('type'));
            if ($type !== 'checkbox') {
                $input->parentNode->removeChild($input);
            }
        }

        // Remove form attributes
        $formAttrs = ['form', 'formaction', 'formmethod', 'formtarget'];
        foreach ($formAttrs as $formAttr) {
            $matchingFormAttrs = $doc->queryXPath('//@' . $formAttr);
            static::removeAttributes($matchingFormAttrs);
        }
    }

    protected function filterOutBadHtmlElementsFromDocument(HtmlDocument $doc): void
    {
        // Remove meta tag to prevent external redirects
        $metaTags = $doc->queryXPath('//meta[' . static::xpathContains('@content', 'url') . ']');
        static::removeNodes($metaTags);
    }

    protected function filterOutNonContentElementsFromDocument(HtmlDocument $doc): void
    {
        // Remove non-content elements
        $formElements = ['link', 'style', 'meta', 'title', 'template'];
        foreach ($formElements as $formElement) {
            $matchingFormElements = $doc->queryXPath('//' . $formElement);
            static::removeNodes($matchingFormElements);
        }
    }

    /**
     * Create an x-path 'contains' statement with a translation automatically built within
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

    /**
     * Alias using the old method name to avoid potential compatibility breaks during patch release.
     * To remove in future feature release.
     * @deprecated Use filterDocument instead.
     */
    public static function removeScriptsFromDocument(HtmlDocument $doc): void
    {
        $config = new HtmlContentFilterConfig(
            filterOutNonContentElements: false,
            useAllowListFilter: false,
        );
        $filter = new self($config);
        $filter->filterDocument($doc);
    }

    /**
     * Alias using the old method name to avoid potential compatibility breaks during patch release.
     * To remove in future feature release.
     * @deprecated Use filterString instead.
     */
    public static function removeScriptsFromHtmlString(string $html): string
    {
        $config = new HtmlContentFilterConfig(
            filterOutNonContentElements: false,
            useAllowListFilter: false,
        );
        $filter = new self($config);
        return $filter->filterString($html);
    }
}

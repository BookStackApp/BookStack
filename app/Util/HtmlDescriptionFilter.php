<?php

namespace BookStack\Util;

use DOMAttr;
use DOMElement;
use DOMNode;

/**
 * Filter to ensure HTML input for description content remains simple and
 * to a limited allow-list of elements and attributes.
 * More for consistency and to prevent nuisance rather than for security
 * (which would be done via a separate content filter and CSP).
 */
class HtmlDescriptionFilter
{
    /**
     * @var array<string, string[]>
     */
    protected static array $allowedAttrsByElements = [
        'p' => [],
        'a' => ['href', 'title', 'target'],
        'ol' => [],
        'ul' => [],
        'li' => [],
        'strong' => [],
        'span' => [],
        'em' => [],
        'br' => [],
    ];

    public static function filterFromString(string $html): string
    {
        if (empty(trim($html))) {
            return '';
        }

        $doc = new HtmlDocument($html);

        $topLevel = [...$doc->getBodyChildren()];
        foreach ($topLevel as $child) {
            /** @var DOMNode $child */
            if ($child instanceof DOMElement) {
                static::filterElement($child);
            } else {
                $child->parentNode->removeChild($child);
            }
        }

        return $doc->getBodyInnerHtml();
    }

    protected static function filterElement(DOMElement $element): void
    {
        $elType = strtolower($element->tagName);
        $allowedAttrs = static::$allowedAttrsByElements[$elType] ?? null;
        if (is_null($allowedAttrs)) {
            $element->remove();
            return;
        }

        $attrs = $element->attributes;
        for ($i = $attrs->length - 1; $i >= 0; $i--) {
            /** @var DOMAttr $attr */
            $attr = $attrs->item($i);
            $name = strtolower($attr->name);
            if (!in_array($name, $allowedAttrs)) {
                $element->removeAttribute($attr->name);
            }
        }

        $childNodes = [...$element->childNodes];
        foreach ($childNodes as $child) {
            if ($child instanceof DOMElement) {
                static::filterElement($child);
            }
        }
    }
}

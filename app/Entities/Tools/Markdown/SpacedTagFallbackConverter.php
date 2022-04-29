<?php

namespace BookStack\Entities\Tools\Markdown;

use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

/**
 * For certain defined tags, add additional spacing upon the retained HTML content
 * to separate it out from anything that may be markdown soon afterwards or within.
 */
class SpacedTagFallbackConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        return \html_entity_decode($element->getChildrenAsString()) . "\n\n";
    }

    public function getSupportedTags(): array
    {
        return ['summary', 'iframe'];
    }
}

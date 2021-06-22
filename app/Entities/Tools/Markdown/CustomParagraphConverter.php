<?php namespace BookStack\Entities\Tools\Markdown;

use League\HTMLToMarkdown\Converter\ParagraphConverter;
use League\HTMLToMarkdown\ElementInterface;

class CustomParagraphConverter extends ParagraphConverter
{
    public function convert(ElementInterface $element): string
    {
        $class = $element->getAttribute('class');
        if (strpos($class, 'callout') !== false) {
            return "<{$element->getTagName()} class=\"{$class}\">{$element->getValue()}</{$element->getTagName()}>\n\n";
        }

        return parent::convert($element);
    }
}

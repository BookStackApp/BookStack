<?php

namespace BookStack\Entities\Tools\Markdown;

use League\HTMLToMarkdown\Converter\DivConverter;
use League\HTMLToMarkdown\ElementInterface;

class CustomDivConverter extends DivConverter
{
    public function convert(ElementInterface $element): string
    {
        // Clean up draw.io diagrams
        $drawIoDiagram = $element->getAttribute('drawio-diagram');
        if ($drawIoDiagram) {
            return "<div drawio-diagram=\"{$drawIoDiagram}\">{$element->getValue()}</div>\n\n";
        }

        return parent::convert($element);
    }
}

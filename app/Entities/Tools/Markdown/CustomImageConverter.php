<?php

namespace BookStack\Entities\Tools\Markdown;

use League\HTMLToMarkdown\Converter\ImageConverter;
use League\HTMLToMarkdown\ElementInterface;

class CustomImageConverter extends ImageConverter
{
    public function convert(ElementInterface $element): string
    {
        $parent = $element->getParent();

        // Remain as HTML if within diagram block.
        $withinDrawing = $parent && !empty($parent->getAttribute('drawio-diagram'));
        if ($withinDrawing) {
            $src = e($element->getAttribute('src'));
            $alt = e($element->getAttribute('alt'));
            return "<img src=\"{$src}\" alt=\"{$alt}\"/>";
        }

        return parent::convert($element);
    }
}

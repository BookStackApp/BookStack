<?php

namespace BookStack\Entities\Tools\Markdown;

use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

class CheckboxConverter implements ConverterInterface
{

    public function convert(ElementInterface $element): string
    {
        if (strtolower($element->getAttribute('type')) === 'checkbox') {
            $isChecked = $element->getAttribute('checked') === 'checked';
            return $isChecked ? ' [x] ' : ' [ ] ';
        }

        return $element->getValue();
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['input'];
    }
}
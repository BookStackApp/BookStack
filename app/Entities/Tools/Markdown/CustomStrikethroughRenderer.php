<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Extension\Strikethrough\Strikethrough;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

/**
 * This is a somewhat clone of the League\CommonMark\Extension\Strikethrough\StrikethroughRender
 * class but modified slightly to use <s> HTML tags instead of <del> in order to
 * match front-end markdown-it rendering.
 */
class CustomStrikethroughRenderer implements InlineRendererInterface
{
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Strikethrough)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        return new HtmlElement('s', $inline->getData('attributes', []), $htmlRenderer->renderInlines($inline->children()));
    }
}

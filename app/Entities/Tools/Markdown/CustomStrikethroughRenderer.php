<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Extension\Strikethrough\Strikethrough;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * This is a somewhat clone of the League\CommonMark\Extension\Strikethrough\StrikethroughRender
 * class but modified slightly to use <s> HTML tags instead of <del> in order to
 * match front-end markdown-it rendering.
 */
class CustomStrikethroughRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        Strikethrough::assertInstanceOf($node);

        return new HtmlElement('s', $node->data->get('attributes'), $childRenderer->renderNodes($node->children()));
    }
}

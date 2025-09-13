<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Renders an AdmonitionBlock node into HTML.
 *
 * Supported block types: note, tip, info, warning, danger, successful.
 * Outputs the raw content of the node and applies the corresponding CSS class.
 */
class AdmonitionBlockRenderer implements NodeRendererInterface
{
    /**
     * Render an AdmonitionBlock node.
     *
     * @param Node $node The node to render.
     * @param mixed $childRenderer The renderer for child nodes.
     * @return HtmlElement
     * @throws \InvalidArgumentException If the node type is not AdmonitionBlock.
     */
    public function render(Node $node, $childRenderer = null)
    {
        if (!($node instanceof AdmonitionBlock)) {
            throw new \InvalidArgumentException('Incompatible node type: ' . get_class($node));
        }

        $type = $node->getType();
        $typeMap = [
            'note' => ['class' => 'callout note'],
            'tip' => ['class' => 'callout tip'],
            'info' => ['class' => 'callout info'],
            'warning' => ['class' => 'callout warning'],
            'danger' => ['class' => 'callout danger'],
            'successful' => ['class' => 'callout successful'],
            'success' => ['class' => 'callout success'],
        ];

        if (!isset($typeMap[$type])) {
            $type = 'note';
        }

        $typeClass = $typeMap[$type]['class'];
        $content = $childRenderer ? $childRenderer->renderNodes($node->children()) : '';
        return new HtmlElement('div', ['class' => $typeClass], $content);
    }
}

<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Renderer\ChildNodeRendererInterface;

/**
 * Class HighlightRenderer
 *
 * Renders Highlight nodes as HTML <mark> elements.
 *
 * @package BookStack\Entities\Tools\Markdown
 */
class HighlightRenderer implements NodeRendererInterface
{
    /**
     * @param Node $node Highlight node
     * @param ChildNodeRendererInterface $childRenderer
     * @return string
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (!($node instanceof Highlight)) {
            throw new \InvalidArgumentException('Node must be instance of Highlight');
        }

        // Escape HTML to prevent injection
        $content = htmlspecialchars($node->getContent(), ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<mark>' . $content . '</mark>';
    }
}

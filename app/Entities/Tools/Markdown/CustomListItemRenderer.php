<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ListItemRenderer;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class CustomListItemRenderer implements NodeRendererInterface
{
    protected ListItemRenderer $baseRenderer;

    public function __construct()
    {
        $this->baseRenderer = new ListItemRenderer();
    }

    /**
     * @return HtmlElement|string|null
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        $listItem = $this->baseRenderer->render($node, $childRenderer);

        if ($node instanceof ListItem && $this->startsTaskListItem($node) && $listItem instanceof HtmlElement) {
            $listItem->setAttribute('class', 'task-list-item');
        }

        return $listItem;
    }

    private function startsTaskListItem(ListItem $block): bool
    {
        $firstChild = $block->firstChild();

        return $firstChild instanceof Paragraph && $firstChild->firstChild() instanceof TaskListItemMarker;
    }
}

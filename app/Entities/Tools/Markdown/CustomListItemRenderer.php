<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Block\Renderer\ListItemRenderer;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use League\CommonMark\HtmlElement;

class CustomListItemRenderer implements BlockRendererInterface
{
    protected $baseRenderer;

    public function __construct()
    {
        $this->baseRenderer = new ListItemRenderer();
    }

    /**
     * @return HtmlElement|string|null
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, bool $inTightList = false)
    {
        $listItem = $this->baseRenderer->render($block, $htmlRenderer, $inTightList);

        if ($this->startsTaskListItem($block)) {
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
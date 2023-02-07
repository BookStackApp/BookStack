<?php

namespace BookStack\Entities\Tools\Markdown;

use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownToHtml
{
    protected string $markdown;

    public function __construct(string $markdown)
    {
        $this->markdown = $markdown;
    }

    public function convert(): string
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());
        $environment->addExtension(new CustomStrikeThroughExtension());
        $environment = Theme::dispatch(ThemeEvents::COMMONMARK_ENVIRONMENT_CONFIGURE, $environment) ?? $environment;
        $converter = new MarkdownConverter($environment);

        $environment->addRenderer(ListItem::class, new CustomListItemRenderer(), 10);

        return $converter->convert($this->markdown)->getContent();
    }
}

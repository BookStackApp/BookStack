<?php

namespace BookStack\Entities\Tools;

use BookStack\Util\HtmlDocument;
use DOMNode;

class PageIncludeContent
{
    protected static array $topLevelTags = ['table', 'ul', 'ol', 'pre'];

    /**
     * @var DOMNode[]
     */
    protected array $contents = [];

    protected bool $isTopLevel = false;

    public function __construct(
        string $html,
        PageIncludeTag $tag,
    ) {
        $this->parseHtml($html, $tag);
    }

    protected function parseHtml(string $html, PageIncludeTag $tag): void
    {
        if (empty($html)) {
            return;
        }

        $doc = new HtmlDocument($html);

        $sectionId = $tag->getSectionId();
        if (!$sectionId) {
            $this->contents = [...$doc->getBodyChildren()];
            $this->isTopLevel = true;
            return;
        }

        $section = $doc->getElementById($sectionId);
        if (!$section) {
            return;
        }

        $isTopLevel = in_array(strtolower($section->nodeName), static::$topLevelTags);
        $this->isTopLevel = $isTopLevel;
        $this->contents = $isTopLevel ? [$section] : [...$section->childNodes];
    }

    public function isInline(): bool
    {
        return !$this->isTopLevel;
    }

    public function isEmpty(): bool
    {
        return empty($this->contents);
    }

    /**
     * @return DOMNode[]
     */
    public function toDomNodes(): array
    {
        return $this->contents;
    }
}

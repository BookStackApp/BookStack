<?php

namespace BookStack\Entities\Tools;

use BookStack\Util\HtmlDocument;
use DOMNode;

class PageIncludeContent
{
    protected static array $topLevelTags = ['table', 'ul', 'ol', 'pre'];

    /**
     * @param DOMNode[] $contents
     * @param bool $isInline
     */
    public function __construct(
        protected array $contents,
        protected bool $isInline,
    ) {
    }

    public static function fromHtmlAndTag(string $html, PageIncludeTag $tag): self
    {
        if (empty($html)) {
            return new self([], true);
        }

        $doc = new HtmlDocument($html);

        $sectionId = $tag->getSectionId();
        if (!$sectionId) {
            $contents = [...$doc->getBodyChildren()];
            return new self($contents, false);
        }

        $section = $doc->getElementById($sectionId);
        if (!$section) {
            return new self([], true);
        }

        $isTopLevel = in_array(strtolower($section->nodeName), static::$topLevelTags);
        $contents = $isTopLevel ? [$section] : [...$section->childNodes];
        return new self($contents, !$isTopLevel);
    }

    public static function fromInlineHtml(string $html): self
    {
        if (empty($html)) {
            return new self([], true);
        }

        $doc = new HtmlDocument($html);

        return new self([...$doc->getBodyChildren()], true);
    }

    public function isInline(): bool
    {
        return $this->isInline;
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

    public function toHtml(): string
    {
        $html = '';

        foreach ($this->contents as $content) {
            $html .= $content->ownerDocument->saveHTML($content);
        }

        return $html;
    }
}

<?php

namespace BookStack\Entities\Tools;

use DOMNode;

class PageIncludeTag
{
    public function __construct(
        public string $tagContent,
        public DOMNode $domNode,
    ) {
    }

    /**
     * Get the page ID that this tag references.
     */
    public function getPageId(): int
    {
        return intval(trim(explode('#', $this->tagContent, 2)[0]));
    }

    /**
     * Get the section ID that this tag references (if any)
     */
    public function getSectionId(): string
    {
        return trim(explode('#', $this->tagContent, 2)[1] ?? '');
    }
}

<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Node\Inline\AbstractInline;

/**
 * Class Highlight
 *
 * Represents a highlighted text node in Markdown.
 *
 * Usage: ==text==
 *
 * @package BookStack\Entities\Tools\Markdown
 */
class Highlight extends AbstractInline
{
    /**
     * @var string The raw text content
     */
    protected string $content;

    /**
     * Highlight constructor.
     *
     * @param string $content The text to highlight
     */
    public function __construct(string $content)
    {
        parent::__construct();
        $this->content = $content;
    }

    /**
     * Get the highlighted content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}

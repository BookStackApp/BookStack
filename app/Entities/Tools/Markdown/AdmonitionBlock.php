<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * Represents an admonition block node for Markdown parsing.
 *
 * Stores the admonition type and acts as a container for child nodes.
 */
class AdmonitionBlock extends AbstractBlock
{
    /**
     * The type of the admonition block (e.g., note, tip, info, warning, danger, successful).
     *
     * @var string
     */
    protected string $type;

    /**
     * AdmonitionBlock constructor.
     *
     * @param string $type The type of the admonition block.
     */
    public function __construct(string $type)
    {
        parent::__construct();
        $this->type = $type;
    }

    /**
     * Get the type of the admonition block.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Indicates if this block can have children.
     *
     * @return bool
     */
    public function canHaveChildren(): bool
    {
        return true;
    }

    /**
     * Indicates if this block is a container.
     *
     * @return bool
     */
    public function isContainer(): bool
    {
        return true;
    }
}

<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Cursor;

/**
 * Parses Markdown admonition blocks.
 *
 * Usage:
 * :::type
 * content
 * :::
 *
 * Where "type" defines the block type, such as info, warning, note, etc.
 */
class AdmonitionBlockParser implements BlockContinueParserInterface
{
    /**
     * The current block node being parsed.
     *
     * @var AdmonitionBlock
     */
    private AdmonitionBlock $block;

    /**
     * Whether the block parsing has finished.
     *
     * @var bool
     */
    private bool $finished = false;

    /**
     * AdmonitionBlockParser constructor.
     *
     * @param string $type Type of the admonition block.
     */
    public function __construct(string $type)
    {
        $this->block = new AdmonitionBlock($type);
    }

    /**
     * Indicates if this parser is a container.
     *
     * @return bool
     */
    public function isContainer(): bool
    {
        return true;
    }

    /**
     * Get the parsed block node.
     *
     * @return AdmonitionBlock
     */
    public function getBlock(): AdmonitionBlock
    {
        return $this->block;
    }

    /**
     * Try to continue parsing the current line.
     *
     * @param Cursor $cursor
     * @param BlockContinueParserInterface|null $activeBlockParser
     * @return BlockContinue|null
     */
    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser = null): ?BlockContinue
    {
        $line = $cursor->getLine();
        // Closing line ::: indicates end of block
        if (preg_match('/^\\s*:::\\s*$/', $line)) {
            $this->finished = true;
            $cursor->advanceToEnd();
            return BlockContinue::finished();
        }
        return $this->finished ? BlockContinue::none() : BlockContinue::at($cursor);
    }

    /**
     * Check if the block parsing is finished.
     *
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * Whether the block can have lazy continuation lines.
     *
     * @return bool
     */
    public function canHaveLazyContinuationLines(): bool
    {
        return false;
    }

    /**
     * Whether the block is a container.
     *
     * @param mixed $block
     * @return bool
     */
    public function canContain($block): bool
    {
        return true;
    }

    /**
     * Add a line to the block.
     *
     * @param string $line
     * @return void
     */
    public function addLine(string $line): void
    {
        // No action needed, let CommonMark handle the content
    }

    /**
     * Close the block.
     *
     * @return void
     */
    public function closeBlock(): void
    {
        // No specific action needed on close
    }
}

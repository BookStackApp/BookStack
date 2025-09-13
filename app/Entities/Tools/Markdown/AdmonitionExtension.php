<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Cursor;

/**
 * Custom CommonMark extension to enable parsing and rendering of Admonition blocks.
 *
 * Registers the AdmonitionBlockParser to parse blocks starting with :::type,
 * and the AdmonitionBlockRenderer to render the blocks into corresponding HTML.
 */
class AdmonitionExtension implements ExtensionInterface
{
    /**
     * Register the extension with a CommonMark environment.
     *
     * @param EnvironmentBuilderInterface $environment
     * @return void
     * @throws \InvalidArgumentException if the environment is invalid
     */
    public function register(EnvironmentBuilderInterface $environment): void
    {
        if (!$environment instanceof EnvironmentBuilderInterface) {
            throw new \InvalidArgumentException('Invalid environment');
        }

        $environment->addBlockStartParser(new class implements BlockStartParserInterface {
            public function tryStart(Cursor $cursor, $parserState): ?BlockStart
            {
                $line = $cursor->getLine();
                if (preg_match('/^:::+\s*([a-zA-Z0-9_-]+)\s*$/', $line, $matches) && !empty($matches[1])) {
                    $type = $matches[1];
                    $cursor->advanceToEnd();
                    return BlockStart::of(new AdmonitionBlockParser($type))->at($cursor);
                }
                return BlockStart::none();
            }
        }, 250)
            ->addRenderer(AdmonitionBlock::class, new AdmonitionBlockRenderer());
    }
}

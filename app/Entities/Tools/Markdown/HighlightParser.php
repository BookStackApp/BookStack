<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Parser\Inline\InlineParserMatch;

/**
 * Class HighlightParser
 *
 * Parses `==text==` in Markdown and generates Highlight nodes.
 *
 * @package BookStack\Entities\Tools\Markdown
 */
class HighlightParser implements InlineParserInterface
{
    /**
     * Defines the match pattern
     *
     * @return InlineParserMatch
     */
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('==(.+?)==');
    }

    /**
     * Parses the matched content and generates a Highlight node
     *
     * @param InlineParserContext $inlineContext
     * @return bool
     */
    public function parse(InlineParserContext $inlineContext): bool
    {
        $match = $inlineContext->getFullMatch();
        if ($match === null) {
            return false;
        }

        // Extract content without the == symbols
        $content = substr($match, 2, -2);

        // Add node to the container
        $inlineContext->getContainer()->appendChild(new Highlight($content));

        // Advance cursor to consume the matched text
        $inlineContext->getCursor()->advanceBy(strlen($match));

        return true;
    }
}

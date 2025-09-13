<?php

namespace BookStack\Entities\Tools\Markdown;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Class HighlightExtension
 *
 * Registers the HighlightParser and HighlightRenderer in the CommonMark environment.
 *
 * @package BookStack\Entities\Tools\Markdown
 */
class HighlightExtension implements ExtensionInterface
{
    /**
     * Register the extension
     *
     * @param EnvironmentBuilderInterface $environment
     */
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addInlineParser(new HighlightParser(), 250)
            ->addRenderer(Highlight::class, new HighlightRenderer());
    }
}

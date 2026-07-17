<?php

namespace BookStack\Util;

readonly class HtmlContentFilterConfig
{
    public function __construct(
        public bool $filterOutJavaScript = true,
        public bool $filterOutBadHtmlElements = true,
        public bool $filterOutFormElements = true,
        public bool $filterOutNonContentElements = true,
        public bool $useAllowListFilter = true,
    ) {
    }

    /**
     * Create an instance from a config string, where the string
     * is a combination of characters to enable filters.
     */
    public static function fromConfigString(string $config): self
    {
        $config = strtolower($config);
        return new self(
            filterOutJavaScript: str_contains($config, 'j'),
            filterOutBadHtmlElements: str_contains($config, 'h'),
            filterOutFormElements: str_contains($config, 'f'),
            filterOutNonContentElements: str_contains($config, 'h'),
            useAllowListFilter: str_contains($config, 'a'),
        );
    }
}

<?php

namespace BookStack\Translation;

class LocaleDefinition
{
    public function __construct(
        protected string $appName,
        protected string $isoName,
        protected bool $isRtl
    ) {
    }

    /**
     * Provide the BookStack-specific locale name.
     */
    public function appLocale(): string
    {
        return $this->appName;
    }

    /**
     * Provide the ISO-aligned locale name.
     */
    public function isoLocale(): string
    {
        return $this->isoName;
    }

    /**
     * Returns a string suitable for the HTML "lang" attribute.
     */
    public function htmlLang(): string
    {
        return str_replace('_', '-', $this->isoName);
    }

    /**
     * Returns a string suitable for the HTML "dir" attribute.
     */
    public function htmlDirection(): string
    {
        return $this->isRtl ? 'rtl' : 'ltr';
    }

    /**
     * Translate using this locate.
     */
    public function trans(string $key, array $replace = []): string
    {
        return trans($key, $replace, $this->appLocale());
    }
}

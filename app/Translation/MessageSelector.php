<?php

namespace BookStack\Translation;

use Illuminate\Translation\MessageSelector as BaseClass;

/**
 * This is a customization of the default Laravel MessageSelector class to tweak pluralization,
 * so that is uses just the first part of the locale string to provide support with
 * non-standard locales such as "de_informal".
 */
class MessageSelector extends BaseClass
{
    public function getPluralIndex($locale, $number)
    {
        $locale = explode('_', $locale)[0];
        return parent::getPluralIndex($locale, $number);
    }
}

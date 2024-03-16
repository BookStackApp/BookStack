<?php

namespace BookStack\Translation;

use Illuminate\Translation\FileLoader as BaseLoader;

class FileLoader extends BaseLoader
{
    /**
     * Load the messages for the given locale.
     *
     * Extends Laravel's translation FileLoader to look in multiple directories
     * so that we can load in translation overrides from the theme file if wanted.
     *
     * Note: As of using Laravel 10, this may now be redundant since Laravel's
     * file loader supports multiple paths. This needs further testing though
     * to confirm if Laravel works how we expect, since we specifically need
     * the theme folder to be able to partially override core lang files.
     *
     * @param string      $locale
     * @param string      $group
     * @param string|null $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        if ($group === '*' && $namespace === '*') {
            return $this->loadJsonPaths($locale);
        }

        if (is_null($namespace) || $namespace === '*') {
            $themePath = theme_path('lang');
            $themeTranslations = $themePath ? $this->loadPaths([$themePath], $locale, $group) : [];
            $originalTranslations = $this->loadPaths($this->paths, $locale, $group);

            return array_merge($originalTranslations, $themeTranslations);
        }

        return $this->loadNamespaced($locale, $group, $namespace);
    }
}

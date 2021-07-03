<?php

namespace BookStack\Translation;

use Illuminate\Translation\FileLoader as BaseLoader;

class FileLoader extends BaseLoader
{
    /**
     * Load the messages for the given locale.
     * Extends Laravel's translation FileLoader to look in multiple directories
     * so that we can load in translation overrides from the theme file if wanted.
     *
     * @param string      $locale
     * @param string      $group
     * @param string|null $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($group === '*' && $namespace === '*') {
            return $this->loadJsonPaths($locale);
        }

        if (is_null($namespace) || $namespace === '*') {
            $themePath = theme_path('lang');
            $themeTranslations = $themePath ? $this->loadPath($themePath, $locale, $group) : [];
            $originalTranslations =  $this->loadPath($this->path, $locale, $group);
            return array_merge($originalTranslations, $themeTranslations);
        }

        return $this->loadNamespaced($locale, $group, $namespace);
    }
}

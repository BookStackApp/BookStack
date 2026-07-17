<?php

namespace BookStack\Translation;

use BookStack\Facades\Theme;
use Illuminate\Translation\FileLoader as BaseLoader;

class FileLoader extends BaseLoader
{
    /**
     * Load the messages for the given locale.
     *
     * Extends Laravel's translation FileLoader to look in multiple directories
     * so that we can load in translation overrides from the theme file if wanted.
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

            $modules = Theme::getModules();
            $moduleTranslations = [];
            foreach ($modules as $module) {
                $modulePath = $module->path('lang');
                if (file_exists($modulePath)) {
                    $moduleTranslations = array_merge($moduleTranslations, $this->loadPaths([$modulePath], $locale, $group));
                }
            }

            $originalTranslations = $this->loadPaths($this->paths, $locale, $group);
            return array_merge($originalTranslations, $moduleTranslations, $themeTranslations);
        }

        return $this->loadNamespaced($locale, $group, $namespace);
    }
}

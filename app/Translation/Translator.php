<?php namespace BookStack\Translation;


class Translator extends \Illuminate\Translation\Translator
{

    /**
     * Mapping of locales to their base locales
     * @var array
     */
    protected $baseLocaleMap = [
        'de_informal' => 'de',
    ];

    /**
     * Get the translation for a given key.
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @return string|array|null
     */
    public function trans($key, array $replace = [], $locale = null)
    {
        $translation = $this->get($key, $replace, $locale);

        if (is_array($translation)) {
            $translation = $this->mergeBackupTranslations($translation, $key, $locale);
        }

        return $translation;
    }

    /**
     * Merge the fallback translations, and base translations if existing,
     * into the provided core key => value array of translations content.
     * @param array $translationArray
     * @param string $key
     * @param null $locale
     * @return array
     */
    protected function mergeBackupTranslations(array $translationArray, string $key, $locale = null)
    {
        $fallback = $this->get($key, [], $this->fallback);
        $baseLocale = $this->getBaseLocale($locale ?? $this->locale);
        $baseTranslations = $baseLocale ? $this->get($key, [], $baseLocale) : [];

        return array_replace_recursive($fallback, $baseTranslations, $translationArray);
    }

    /**
     * Get the array of locales to be checked.
     *
     * @param  string|null  $locale
     * @return array
     */
    protected function localeArray($locale)
    {
        $primaryLocale = $locale ?: $this->locale;
        return array_filter([$primaryLocale, $this->getBaseLocale($primaryLocale), $this->fallback]);
    }

    /**
     * Get the locale to extend for the given locale.
     *
     * @param string $locale
     * @return string|null
     */
    protected function getBaseLocale($locale)
    {
        return $this->baseLocaleMap[$locale] ?? null;
    }

}
<?php

namespace BookStack\Translation;

use BookStack\Users\Models\User;
use Illuminate\Http\Request;

class LanguageManager
{
    /**
     * Array of right-to-left language options.
     */
    protected array $rtlLanguages = ['ar', 'fa', 'he'];

    /**
     * Map of BookStack language names to best-estimate ISO and windows locale names.
     * Locales can often be found by running `locale -a` on a linux system.
     * Windows locales can be found at:
     * https://docs.microsoft.com/en-us/cpp/c-runtime-library/language-strings?view=msvc-170.
     *
     * @var array<string, array{iso: string, windows: string}>
     */
    protected array $localeMap = [
        'ar'          => ['iso' => 'ar', 'windows' => 'Arabic'],
        'bg'          => ['iso' => 'bg_BG', 'windows' => 'Bulgarian'],
        'bs'          => ['iso' => 'bs_BA', 'windows' => 'Bosnian (Latin)'],
        'ca'          => ['iso' => 'ca', 'windows' => 'Catalan'],
        'cs'          => ['iso' => 'cs_CZ', 'windows' => 'Czech'],
        'da'          => ['iso' => 'da_DK', 'windows' => 'Danish'],
        'de'          => ['iso' => 'de_DE', 'windows' => 'German'],
        'de_informal' => ['iso' => 'de_DE', 'windows' => 'German'],
        'en'          => ['iso' => 'en_GB', 'windows' => 'English'],
        'el'          => ['iso' => 'el_GR', 'windows' => 'Greek'],
        'es'          => ['iso' => 'es_ES', 'windows' => 'Spanish'],
        'es_AR'       => ['iso' => 'es_AR', 'windows' => 'Spanish'],
        'et'          => ['iso' => 'et_EE', 'windows' => 'Estonian'],
        'eu'          => ['iso' => 'eu_ES', 'windows' => 'Basque'],
        'fa'          => ['iso' => 'fa_IR', 'windows' => 'Persian'],
        'fr'          => ['iso' => 'fr_FR', 'windows' => 'French'],
        'he'          => ['iso' => 'he_IL', 'windows' => 'Hebrew'],
        'hr'          => ['iso' => 'hr_HR', 'windows' => 'Croatian'],
        'hu'          => ['iso' => 'hu_HU', 'windows' => 'Hungarian'],
        'id'          => ['iso' => 'id_ID', 'windows' => 'Indonesian'],
        'it'          => ['iso' => 'it_IT', 'windows' => 'Italian'],
        'ja'          => ['iso' => 'ja', 'windows' => 'Japanese'],
        'ko'          => ['iso' => 'ko_KR', 'windows' => 'Korean'],
        'lt'          => ['iso' => 'lt_LT', 'windows' => 'Lithuanian'],
        'lv'          => ['iso' => 'lv_LV', 'windows' => 'Latvian'],
        'nl'          => ['iso' => 'nl_NL', 'windows' => 'Dutch'],
        'nb'          => ['iso' => 'nb_NO', 'windows' => 'Norwegian (Bokmal)'],
        'pl'          => ['iso' => 'pl_PL', 'windows' => 'Polish'],
        'pt'          => ['iso' => 'pt_PT', 'windows' => 'Portuguese'],
        'pt_BR'       => ['iso' => 'pt_BR', 'windows' => 'Portuguese'],
        'ro'          => ['iso' => 'ro_RO', 'windows' => 'Romanian'],
        'ru'          => ['iso' => 'ru', 'windows' => 'Russian'],
        'sk'          => ['iso' => 'sk_SK', 'windows' => 'Slovak'],
        'sl'          => ['iso' => 'sl_SI', 'windows' => 'Slovenian'],
        'sv'          => ['iso' => 'sv_SE', 'windows' => 'Swedish'],
        'uk'          => ['iso' => 'uk_UA', 'windows' => 'Ukrainian'],
        'vi'          => ['iso' => 'vi_VN', 'windows' => 'Vietnamese'],
        'zh_CN'       => ['iso' => 'zh_CN', 'windows' => 'Chinese (Simplified)'],
        'zh_TW'       => ['iso' => 'zh_TW', 'windows' => 'Chinese (Traditional)'],
        'tr'          => ['iso' => 'tr_TR', 'windows' => 'Turkish'],
    ];

    /**
     * Get the language specifically for the currently logged-in user if available.
     */
    public function getUserLanguage(Request $request, string $default): string
    {
        try {
            $user = user();
        } catch (\Exception $exception) {
            return $default;
        }

        if ($user->isDefault() && config('app.auto_detect_locale')) {
            return $this->autoDetectLocale($request, $default);
        }

        return setting()->getUser($user, 'language', $default);
    }

    /**
     * Get the language for the given user.
     */
    public function getLanguageForUser(User $user): string
    {
        $default = config('app.locale');
        return setting()->getUser($user, 'language', $default);
    }

    /**
     * Check if the given BookStack language value is a right-to-left language.
     */
    public function isRTL(string $language): bool
    {
        return in_array($language, $this->rtlLanguages);
    }

    /**
     * Autodetect the visitors locale by matching locales in their headers
     * against the locales supported by BookStack.
     */
    protected function autoDetectLocale(Request $request, string $default): string
    {
        $availableLocales = config('app.locales');
        foreach ($request->getLanguages() as $lang) {
            if (in_array($lang, $availableLocales)) {
                return $lang;
            }
        }

        return $default;
    }

    /**
     * Get the ISO version of a BookStack language name.
     */
    public function getIsoName(string $language): string
    {
        return $this->localeMap[$language]['iso'] ?? $language;
    }

    /**
     * Set the system date locale for localized date formatting.
     * Will try both the standard locale name and the UTF8 variant.
     */
    public function setPhpDateTimeLocale(string $language): void
    {
        $isoLang = $this->localeMap[$language]['iso'] ?? '';
        $isoLangPrefix = explode('_', $isoLang)[0];

        $locales = array_values(array_filter([
            $isoLang ? $isoLang . '.utf8' : false,
            $isoLang ?: false,
            $isoLang ? str_replace('_', '-', $isoLang) : false,
            $isoLang ? $isoLangPrefix . '.UTF-8' : false,
            $this->localeMap[$language]['windows'] ?? false,
            $language,
        ]));

        if (!empty($locales)) {
            setlocale(LC_TIME, $locales[0], ...array_slice($locales, 1));
        }
    }
}

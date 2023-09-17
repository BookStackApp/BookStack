<?php

namespace BookStack\Translation;

use BookStack\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LocaleManager
{
    /**
     * Array of right-to-left locale options.
     */
    protected array $rtlLocales = ['ar', 'fa', 'he'];

    /**
     * Map of BookStack locale names to best-estimate ISO and windows locale names.
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
        'el'          => ['iso' => 'el_GR', 'windows' => 'Greek'],
        'en'          => ['iso' => 'en_GB', 'windows' => 'English'],
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
        'nb'          => ['iso' => 'nb_NO', 'windows' => 'Norwegian (Bokmal)'],
        'nl'          => ['iso' => 'nl_NL', 'windows' => 'Dutch'],
        'pl'          => ['iso' => 'pl_PL', 'windows' => 'Polish'],
        'pt'          => ['iso' => 'pt_PT', 'windows' => 'Portuguese'],
        'pt_BR'       => ['iso' => 'pt_BR', 'windows' => 'Portuguese'],
        'ro'          => ['iso' => 'ro_RO', 'windows' => 'Romanian'],
        'ru'          => ['iso' => 'ru', 'windows' => 'Russian'],
        'sk'          => ['iso' => 'sk_SK', 'windows' => 'Slovak'],
        'sl'          => ['iso' => 'sl_SI', 'windows' => 'Slovenian'],
        'sv'          => ['iso' => 'sv_SE', 'windows' => 'Swedish'],
        'tr'          => ['iso' => 'tr_TR', 'windows' => 'Turkish'],
        'uk'          => ['iso' => 'uk_UA', 'windows' => 'Ukrainian'],
        'uz'          => ['iso' => 'uz_UZ', 'windows' => 'Uzbek'],
        'vi'          => ['iso' => 'vi_VN', 'windows' => 'Vietnamese'],
        'zh_CN'       => ['iso' => 'zh_CN', 'windows' => 'Chinese (Simplified)'],
        'zh_TW'       => ['iso' => 'zh_TW', 'windows' => 'Chinese (Traditional)'],
    ];

    /**
     * Get the BookStack locale string for the given user.
     */
    protected function getLocaleForUser(User $user): string
    {
        $default = config('app.default_locale');

        if ($user->isGuest() && config('app.auto_detect_locale')) {
            return $this->autoDetectLocale(request(), $default);
        }

        return setting()->getUser($user, 'language', $default);
    }

    /**
     * Get a locale definition for the current user.
     */
    public function getForUser(User $user): LocaleDefinition
    {
        $localeString = $this->getLocaleForUser($user);

        return new LocaleDefinition(
            $localeString,
            $this->getIsoName($localeString),
            in_array($localeString, $this->rtlLocales),
        );
    }

    /**
     * Autodetect the visitors locale by matching locales in their headers
     * against the locales supported by BookStack.
     */
    protected function autoDetectLocale(Request $request, string $default): string
    {
        $availableLocales = array_keys($this->localeMap);

        foreach ($request->getLanguages() as $lang) {
            if (in_array($lang, $availableLocales)) {
                return $lang;
            }
        }

        return $default;
    }

    /**
     * Get the ISO version of a BookStack locale.
     */
    protected function getIsoName(string $locale): string
    {
        return $this->localeMap[$locale]['iso'] ?? $locale;
    }

    /**
     * Sets the active locale for system level components.
     */
    public function setAppLocale(LocaleDefinition $locale): void
    {
        app()->setLocale($locale->appLocale());
        Carbon::setLocale($locale->isoLocale());
        $this->setPhpDateTimeLocale($locale);
    }

    /**
     * Set the system date locale for localized date formatting.
     * Will try both the standard locale name and the UTF8 variant.
     */
    public function setPhpDateTimeLocale(LocaleDefinition $locale): void
    {
        $appLocale = $locale->appLocale();
        $isoLocale = $this->localeMap[$appLocale]['iso'] ?? '';
        $isoLocalePrefix = explode('_', $isoLocale)[0];

        $locales = array_values(array_filter([
            $isoLocale ? $isoLocale . '.utf8' : false,
            $isoLocale ?: false,
            $isoLocale ? str_replace('_', '-', $isoLocale) : false,
            $isoLocale ? $isoLocalePrefix . '.UTF-8' : false,
            $this->localeMap[$appLocale]['windows'] ?? false,
            $appLocale,
        ]));

        if (!empty($locales)) {
            setlocale(LC_TIME, $locales[0], ...array_slice($locales, 1));
        }
    }
}
